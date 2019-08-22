<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Swoole;


use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Server\ServiceServerEvent;
use Swoft\Server\Contract\CloseInterface;
use Swoole\Server;

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
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     *
     * @throws ContainerException*
     */
    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        // Before close
        Swoft::trigger(ServiceServerEvent::BEFORE_CLOSE, null, $server, $fd, $reactorId);

        // Close event
        Swoft::trigger(ServiceServerEvent::CLOSE);

        // After close
        Swoft::trigger(ServiceServerEvent::AFTER_CLOSE);
    }
}