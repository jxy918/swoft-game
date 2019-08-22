<?php declare(strict_types=1);

namespace Swoft\Listener;

use Swoft\Console\Console;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Server\Event\ServerStartEvent;
use Swoft\Server\SwooleEvent;

/**
 * Class ManagerStartListener
 *
 * @since 2.0
 *
 * @Listener(SwooleEvent::MANAGER_START)
 */
class ManagerStartListener implements EventHandlerInterface
{
    /**
     * @param EventInterface|ServerStartEvent $event
     */
    public function handle(EventInterface $event): void
    {
        $server = $event->coServer;

       Console::writef(
           'Server start success (Master PID: <mga>%d</mga>, Manager PID: <mga>%d</mga>)',
           $server->master_pid,
           $server->manager_pid
       );
    }
}
