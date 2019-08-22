<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Swoole;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Co;
use Swoft\Context\Context;
use Swoft\Http\Message\Request as Psr7Request;
use Swoft\Http\Message\Response as Psr7Response;
use Swoft\Server\Contract\HandshakeInterface;
use Swoft\Session\Session;
use Swoft\SwoftEvent;
use Swoft\WebSocket\Server\Connection;
use Swoft\WebSocket\Server\Context\WsHandshakeContext;
use Swoft\WebSocket\Server\Contract\WsModuleInterface;
use Swoft\WebSocket\Server\Helper\WsHelper;
use Swoft\WebSocket\Server\WsDispatcher;
use Swoft\WebSocket\Server\WsErrorDispatcher;
use Swoft\WebSocket\Server\WsServerEvent;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Throwable;
use function server;

/**
 * Class HandshakeListener
 *
 * @since 2.0
 *
 * @Bean()
 */
class HandshakeListener implements HandshakeInterface
{
    /**
     * Ws Handshake event
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     * @throws Throwable
     */
    public function onHandshake(Request $request, Response $response): bool
    {
        $fd  = $request->fd;
        $sid = (string)$fd;

        server()->log("Handshake: conn#$fd start an websocket connection request", [], 'debug');

        // If sec-websocket-key error
        $status = false;
        $secKey = $request->header['sec-websocket-key'];
        if (WsHelper::isInvalidSecKey($secKey)) {
            server()->log("Handshake: shake hands failed with the #$fd. 'sec-websocket-key' is error!");
            $response->end('sec-websocket-key is invalid!');
            return false;
        }

        // Initialize psr7 Request and Response
        $psr7Req = Psr7Request::new($request);
        $psr7Res = Psr7Response::new($response);

        // Initialize connection session and context
        $ctx  = WsHandshakeContext::new($psr7Req, $psr7Res);
        $conn = Connection::new($fd, $psr7Req, $psr7Res);

        // Bind connection and bind cid => sid(fd)
        Session::set($sid, $conn);
        // Storage context
        Context::set($ctx);

        try {
            Swoft::trigger(WsServerEvent::HANDSHAKE_BEFORE, $fd, $request, $response);

            /** @var WsDispatcher $dispatcher */
            $dispatcher = BeanFactory::getSingleton('wsDispatcher');

            /** @var Psr7Response $psr7Res */
            [$status, $psr7Res] = $dispatcher->handshake($psr7Req, $psr7Res);
            if (true !== $status) {
                server()->log("Handshake: conn#$fd handshake check failed");
                $psr7Res->quickSend();

                // NOTICE: Rejecting a handshake still triggers a close event.
                return false;
            }

            // Config response
            $psr7Res = $psr7Res->withStatus(101)->withHeaders(WsHelper::handshakeHeaders($secKey));
            if ($wsProtocol = $request->header['sec-websocket-protocol'] ?? '') {
                $psr7Res = $psr7Res->withHeader('Sec-WebSocket-Protocol', $wsProtocol);
            }

            // Response handshake successfully
            $meta = $conn->getMetadata();
            $conn->setHandshake(true);
            $psr7Res->quickSend();

            server()->log("Handshake: conn#{$fd} handshake successful! meta:", $meta, 'debug');
            Swoft::trigger(WsServerEvent::HANDSHAKE_SUCCESS, $fd, $request, $response);

            // Handshaking successful, Manually triggering the open event
            Co::create(function () use ($psr7Req, $fd) {
                $this->onOpen($psr7Req, $fd);
            });
        } catch (Throwable $e) {
            Swoft::trigger(WsServerEvent::HANDSHAKE_ERROR, $e, $request);

            /** @var WsErrorDispatcher $errDispatcher */
            $errDispatcher = BeanFactory::getSingleton(WsErrorDispatcher::class);

            // Handle handshake error
            $psr7Res = $errDispatcher->handshakeError($e, $psr7Res);
            $psr7Res->quickSend();
        } finally {
            // Defer
            Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

            // Destroy
            Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);

            // Unbind cid => sid(fd)
            Session::unbindCo();
        }

        return $status;
    }

    /**
     * @param Psr7Request $request
     * @param int         $fd
     *
     * @throws Throwable
     */
    public function onOpen(Psr7Request $request, int $fd): void
    {
        // Bind cid => sid(fd)
        Session::bindCo((string)$fd);

        $server = server()->getSwooleServer();
        server()->log("Open: conn#{$fd} has been opened", [], 'debug');

        try {
            /** @var Connection $conn */
            $conn = Session::mustGet();
            $info = $conn->getModuleInfo();

            if ($method = $info['eventMethods']['open'] ?? '') {
                $class = $info['class'];

                server()->log("Open: conn#{$fd} call ws open handler '{$class}::{$method}'", [], 'debug');

                /** @var WsModuleInterface $module */
                $module = BeanFactory::getSingleton($class);
                $module->$method($request, $fd);
            }

            Swoft::trigger(WsServerEvent::OPEN_AFTER, $fd, $server, $request);
        } catch (Throwable $e) {
            Swoft::trigger(WsServerEvent::OPEN_ERROR, $e, $request);

            /** @var WsErrorDispatcher $errDispatcher */
            $errDispatcher = BeanFactory::getSingleton(WsErrorDispatcher::class);
            $errDispatcher->openError($e, $request);
        } finally {
            // Unbind cid => sid(fd)
            Session::unbindCo();
        }
    }
}
