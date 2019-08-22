<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Swoole;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Server\Contract\CloseInterface;
use Swoft\Session\Session;
use Swoft\SwoftEvent;
use Swoft\Tcp\Server\Connection;
use Swoft\Tcp\Server\Context\TcpCloseContext;
use Swoft\Tcp\Server\TcpErrorDispatcher;
use Swoft\Tcp\Server\TcpServerEvent;
use Swoole\Server;
use Throwable;

/**
 * Class CloseListener
 *
 * @since 2.0
 * @Bean()
 */
class CloseListener implements CloseInterface
{
    /**
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        $sid = (string)$fd;
        // TODO handle close other worker connection
        $ctx = TcpCloseContext::new($fd, $reactorId);

        // Storage context
        Context::set($ctx);
        // Bind cid => sid(fd)
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

            // Trigger event
            Swoft::trigger(TcpServerEvent::CLOSE, $fd, $server, $reactorId);
        } catch (Throwable $e) {
            server()->log("Close: conn#{$fd} error on handle close, ERR: " . $e->getMessage(), [], 'error');
            Swoft::trigger(TcpServerEvent::CLOSE_ERROR, $e, $fd);

            /** @var TcpErrorDispatcher $errDispatcher */
            $errDispatcher = BeanFactory::getSingleton(TcpErrorDispatcher::class);
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
