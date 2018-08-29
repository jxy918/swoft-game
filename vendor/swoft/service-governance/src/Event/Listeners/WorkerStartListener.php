<?php

namespace Swoft\Sg\Event\Listeners;

use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Event\AppEvent;

/**
 * Worker start listener
 *
 * @Listener(AppEvent::WORKER_START)
 */
class WorkerStartListener implements EventHandlerInterface
{
    /**
     * @param \Swoft\Event\EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        provider()->select()->registerService();
    }
}