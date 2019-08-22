<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Swoole;

use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Server\Contract\CloseInterface;
use Swoft\Session\Session;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\SwoftEvent;
use Swoft\WebSocket\Server\Connection;
use Swoft\WebSocket\Server\Context\WsCloseContext;
use Swoft\WebSocket\Server\WsDispatcher;
use Swoft\WebSocket\Server\WsErrorDispatcher;
use Swoft\WebSocket\Server\WsServerEvent;
use Swoole\Server;
use Throwable;
use function server;

/**
 * Class CloseListener
 *
 * @since 2.0
 *
 * @Bean()
 */
class CloseListener implements CloseInterface
{
    /**
     * Close event
     *
     * @param Server|\Swoole\WebSocket\Server $server
     * @param int                             $fd
     * @param int                             $reactorId
     *
     * @throws Throwable
     */
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        // Only allow handshake success connection
        if (!$server->isEstablished($fd)) {
            return;
        }

        $sid = (string)$fd;

        // Session data not exist the worker, notify other worker clear session.
        if (!Session::has($sid)) {
            $data = [
                'from'  => 'wsServer',
                'event' => 'wsClose',
                'fd'    => $fd,
                'sid'   => $sid,
            ];

            // $server->sendMessage($message, $dst_worker_id);
            server()->log("Close: conn#{$fd} session not exist current worker, notify other worker handle");
            server()->notifyWorkers(JsonHelper::encode($data), [], [$server->worker_id]);
            return;
        }

        $ctx = WsCloseContext::new($fd, $reactorId);

        // Storage context
        Context::set($ctx);
        // Unbind cid => sid(fd)
        Session::bindCo($sid);

        try {
            /** @var Connection $conn */
            $conn  = Session::mustGet();
            $total = server()->count() - 1;

            server()->log("Close: conn#{$fd} has been closed. server conn count $total", [], 'debug');
            if (!$meta = $conn->getMetadata()) {
                server()->log("Close: conn#{$fd} connection meta info has been lost");
                return;
            }

            server()->log("Close: conn#{$fd} meta info:", $meta, 'debug');

            // Handshake successful callback close handle
            if ($conn->isHandshake()) {
                /** @var WsDispatcher $dispatcher */
                $dispatcher = Swoft::getSingleton('wsDispatcher');
                $dispatcher->close($server, $fd);
            }

            // Call on close callback
            Swoft::trigger(WsServerEvent::AFTER_CLOSE, $fd, $server);
        } catch (Throwable $e) {
            server()->log("Close: conn#{$fd} error on handle close, ERR: " . $e->getMessage(), [], 'error');
            Swoft::trigger(WsServerEvent::CLOSE_ERROR, $e, $fd);

            /** @var WsErrorDispatcher $errDispatcher */
            $errDispatcher = Swoft::getSingleton(WsErrorDispatcher::class);
            $errDispatcher->closeError($e, $fd);
        } finally {
            // Defer
            Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

            // Destroy
            Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);

            // Remove connection
            Swoft::trigger(SwoftEvent::SESSION_COMPLETE, $sid);

            // Unbind cid => sid(fd)
            Session::unbindCo();
        }
    }
}
