<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Testing\Listener;

use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Tcp\Server\TcpServerEvent;
use SwoftTest\Tcp\Server\Testing\TempString;

/**
 * Class TcpConnectListener
 *
 * @Listener(TcpServerEvent::CONNECT)
 */
class TcpConnectListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $evt
     */
    public function handle(EventInterface $evt): void
    {
        TempString::add($evt->getName(), 'fd', $evt->getParam(0), 'rd', $evt->getParam(1));
    }
}
