<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Swoole;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Server\Contract\ReceiveInterface;
use Swoft\Session\Session;
use Swoft\SwoftEvent;
use Swoft\Tcp\Server\Context\TcpReceiveContext;
use Swoft\Tcp\Server\Exception\TcpResponseException;
use Swoft\Tcp\Server\Request;
use Swoft\Tcp\Server\Response;
use Swoft\Tcp\Server\TcpDispatcher;
use Swoft\Tcp\Server\TcpErrorDispatcher;
use Swoft\Tcp\Server\TcpServerEvent;
use Swoole\Server;
use Throwable;

/**
 * Class ReceiveListener
 *
 * @since 2.0
 * @Bean()
 */
class ReceiveListener implements ReceiveInterface
{
    /**
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     * @param string $data
     *
     * @throws ContainerException
     * @throws ReflectionException
     * @throws TcpResponseException
     */
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): void
    {
        $response = Response::new($fd);
        $request  = Request::new($fd, $data, $reactorId);

        server()->log("Receive: conn#{$fd} received client request, begin init context", [], 'debug');

        $sid = (string)$fd;
        $ctx = TcpReceiveContext::new($fd, $request, $response);

        // Storage context
        Context::set($ctx);
        // Bind cid => sid(fd)
        Session::bindCo($sid);

        try {
            // Trigger receive event
            Swoft::trigger(TcpServerEvent::RECEIVE, $fd, $server, $reactorId);

            /** @var TcpDispatcher $dispatcher */
            $dispatcher = Swoft::getSingleton('tcpDispatcher');

            // Dispatching. allow user disable dispatch.
            if ($dispatcher->isEnable()) {
                $response = $dispatcher->dispatch($request, $response);

                // Trigger package send event
                Swoft::trigger(TcpServerEvent::PACKAGE_SEND, $response);

                $response->send($server);
            }
        } catch (Throwable $e) {
            server()->log("Receive: conn#{$fd} error: " . $e->getMessage(), [], 'error');
            Swoft::trigger(TcpServerEvent::RECEIVE_ERROR, $e, $fd);

            /** @var TcpErrorDispatcher $errDispatcher */
            $errDispatcher = Swoft::getSingleton(TcpErrorDispatcher::class);

            // Dispatching error handle
            $response = $errDispatcher->receiveError($e, $response);
            $response->send($server);
        } finally {
            // Defer
            Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

            // Destroy
            Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);

            // Unbind cid => sid(fd)
            Session::unbindCo();
        }
    }
}
