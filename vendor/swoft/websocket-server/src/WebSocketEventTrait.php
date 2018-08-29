<?php

namespace Swoft\WebSocket\Server;

use Swoft\App;
use Swoft\Core\Coroutine;
use Swoft\WebSocket\Server\Event\WsEvent;
use Swoft\WebSocket\Server\Router\Dispatcher;
use \Swoft\Http\Message\Server\Request as Psr7Request;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Trait HandshakeTrait - handle ws event in the swoole
 * @package Swoft\WebSocket\Server
 */
trait WebSocketEventTrait
{
    /**
     * webSocket 建立连接后进行握手。WebSocket服务器已经内置了handshake，
     * 如果用户希望自己进行握手处理，可以设置 onHandShake 事件回调函数。
     * @param Request $request
     * @param Response $response
     * @return bool
     * @throws \Swoft\WebSocket\Server\Exception\WsRouteException
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function onHandShake(Request $request, Response $response): bool
    {
        $fd = $request->fd;
        $secWSKey = $request->header['sec-websocket-key'];

        // sec-websocket-key 错误
        if (WebSocket::isInvalidSecWSKey($secWSKey)) {
            $this->log("Handshake: shake hands failed with the #$fd. 'sec-websocket-key' is error!");

            return false;
        }

        // Initialize psr7 Request and Response and metadata
        $cid = Coroutine::tid();
        $meta = $this->buildConnectionMetadata($fd, $request);
        $psr7Req = Psr7Request::loadFromSwooleRequest($request);
        $psr7Res = new \Swoft\Http\Message\Server\Response($response);

        // Initialize client information
        WebSocketContext::init($fd, $meta, $psr7Req);

        // init fd and coId mapping
        WebSocketContext::setFdToCoId($fd);

        // $this->log(
        //     "Handshake: Ready to shake hands with the #$fd client connection, path {$meta['path']}, co ID #$cid. request headers:\n" .
        //     $psr7Req->getHeaders()
        // );

        App::trigger(WsEvent::ON_HANDSHAKE, null, $request, $response, $fd);

        /** @var Dispatcher $dispatcher */
        $dispatcher = \bean('wsDispatcher');

        /** @var \Swoft\Http\Message\Server\Response $psr7Res */
        list($status, $psr7Res) = $dispatcher->handshake($psr7Req, $psr7Res);

        // handshake check is failed -- 拒绝连接，比如需要认证，限定路由，限定ip，限定domain等
        if (HandlerInterface::HANDSHAKE_OK !== $status) {
            $this->log("Client #$fd handshake check failed, request path {$meta['path']}");
            $psr7Res->send();

            // NOTICE: Rejecting a handshake still triggers a close event.
            return false;
        }

        // setting response
        $psr7Res = $psr7Res->withStatus(101)->withHeaders(WebSocket::handshakeHeaders($secWSKey));

        if (isset($request->header['sec-websocket-protocol'])) {
            $psr7Res = $psr7Res->withHeader('Sec-WebSocket-Protocol', $request->header['sec-websocket-protocol']);
        }

        // $this->log("Handshake: response headers:\n", $psr7Res->getHeaders());

        // Response handshake successfully
        $psr7Res->send();

        WebSocketContext::setMeta($fd, true, 'handshake');

        $this->log(
            "Handshake: Client #{$fd} handshake successful! path {$meta['path']}, co Id #$cid, Meta:",
            WebSocketContext::getMeta(null, $fd),
            'debug'
        );

        // Handshaking successful, Manually triggering the open event
        $this->server->defer(function () use ($psr7Req, $fd) {
            $this->onWsOpen($this->server, $psr7Req, $fd);

        });

        // delete coId to fd mapping
        WebSocketContext::delFdByCoId();

        return true;
    }

    /**
     * @param int $fd
     * @param Request $request
     * @return array
     */
    protected function buildConnectionMetadata(int $fd, Request $request): array
    {
        $info = $this->getClientInfo($fd);
        $path = \parse_url($request->server['request_uri'], \PHP_URL_PATH);

        $this->log("onHandShake: Client #{$fd} send handshake request to {$path}, client info: ", $info, 'debug');

        return [
            'fd' => $fd,
            'ip' => $info['remote_ip'],
            'port' => $info['remote_port'],
            'path' => $path,
            'handshake' => false,
            'connectTime' => $info['connect_time'],
            'handshakeTime' => \microtime(true),
        ];
    }

    /**
     * @param Server $server
     * @param Psr7Request $request
     * @param int $fd
     * @throws \InvalidArgumentException
     */
    public function onWsOpen(Server $server, Psr7Request $request, int $fd)
    {
        App::trigger(WsEvent::ON_OPEN, null, $server, $request, $fd);

        $this->log("connection #$fd has been opened, co ID #" . Coroutine::tid(), [], 'debug');

        /** @see Dispatcher::open() */
        \bean('wsDispatcher')->open($server, $request, $fd);
    }

    /**
     * When you receive the message
     * @param  Server $server
     * @param  Frame $frame
     * @throws \Swoft\WebSocket\Server\Exception\WsRouteException
     * @throws \Swoft\WebSocket\Server\Exception\WsMessageException
     * @throws \InvalidArgumentException
     */
    public function onMessage(Server $server, Frame $frame)
    {
        $fd = $frame->fd;

        // init fd and coId mapping
        WebSocketContext::setFdToCoId($fd);

        App::trigger(WsEvent::ON_MESSAGE, null, $server, $frame);

        $this->log("received message: {$frame->data} from fd #{$fd}, co ID #" . Coroutine::tid(), [], 'debug');

        /** @see Dispatcher::message() */
        \bean('wsDispatcher')->message($server, $frame);

        // delete coId to fd mapping
        WebSocketContext::delFdByCoId();
    }

    /**
     * on webSocket close
     * - 注意：close 事件触发时，已经不能给客户端发消息了
     * @param  Server $server
     * @param  int $fd
     * @throws \InvalidArgumentException
     */
    public function onClose(Server $server, int $fd)
    {
        /*
        WEBSOCKET_STATUS_CONNECTION = 1，连接进入等待握手
        WEBSOCKET_STATUS_HANDSHAKE = 2，正在握手
        WEBSOCKET_STATUS_FRAME = 3，已握手成功等待浏览器发送数据帧
        */
        $fdInfo = $this->getClientInfo($fd);

        if ($fdInfo['websocket_status'] > 0) {
            $total = $this->count();
            $this->log("onClose: Client #{$fd} connection has been closed. client count $total, client info:", $fdInfo, 'debug');

            if (!$meta = WebSocketContext::getMeta(null, $fd)) {
                $this->log("onClose: Client #{$fd} connection meta info has been lost");
                return;
            }

            $this->log("onClose: Client #{$fd} meta info:", $meta, 'debug');

            // 握手成功的才回调 close
            if ($meta['handshake']) {
                /** @see Dispatcher::close() */
                \bean('wsDispatcher')->close($server, $fd);
            }

            // call on close callback
            App::trigger(WsEvent::ON_CLOSE, null, $server, $fd);

            // clear context info of the connection
            WebSocketContext::del($fd);
        }
    }
}
