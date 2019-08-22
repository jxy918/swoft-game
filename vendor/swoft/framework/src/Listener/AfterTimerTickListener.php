<?php declare(strict_types=1);


namespace Swoft\Listener;


use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\SwoftEvent;
use Swoft\Event\Annotation\Mapping\Listener;

/**
 * Class AfterTimerTickListener
 *
 * @since 2.0
 *
 * @Listener(event=SwoftEvent::TIMER_TICK_AFTER)
 */
class AfterTimerTickListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        // Defer
        Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

        // Destroy
        Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);
    }
}