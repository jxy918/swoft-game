<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Swoole;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Server\Contract\ConnectInterface;
use Swoft\Session\Session;
use Swoft\SwoftEvent;
use Swoft\Tcp\Server\Connection;
use Swoft\Tcp\Server\Context\TcpConnectContext;
use Swoft\Tcp\Server\TcpDispatcher;
use Swoft\Tcp\Server\TcpErrorDispatcher;
use Swoft\Tcp\Server\TcpServerEvent;
use Swoole\Server;
use Throwable;

/**
 * Class ConnectListener
 *
 * @since 2.0.3
 * @Bean()
 */
class ConnectListener implements ConnectInterface
{
    /**
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function onConnect(Server $server, int $fd, int $reactorId): void
    {
        $sid = (string)$fd;

        // Initialize connection session and context
        $ctx  = TcpConnectContext::new($fd, $reactorId);
        $conn = Connection::new($fd, (array)$server->getClientInfo($fd));

        // Bind session connection and bind cid => sid(fd)
        Session::set($sid, $conn);
        // Storage context
        Context::set($ctx);

        try {
            // Trigger connect event
            Swoft::trigger(TcpServerEvent::CONNECT, $server, $fd, $reactorId);

            /** @var TcpDispatcher $dispatcher */
            // $dispatcher = Swoft::getSingleton('tcpDispatcher');
        } catch (Throwable $e) {
            Swoft::trigger(TcpServerEvent::CONNECT_ERROR, $e, $fd);

            /** @var TcpErrorDispatcher $errDispatcher */
            $errDispatcher = Swoft::getSingleton(TcpErrorDispatcher::class);

            // Handle connect error
            $errDispatcher->connectError($e, $fd);
        } finally {
            // Trigger defer event
            Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

            // Trigger coroutine destroy event
            Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);

            // Unbind cid => sid(fd)
            Session::unbindCo();
        }
    }
}
