<?php declare(strict_types=1);


namespace Swoft\Listener;


use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\SwoftEvent;
use Swoft\Event\Annotation\Mapping\Listener;

/**
 * Class AfterTimerAfterListener
 *
 * @since 2.0
 *
 * @Listener(event=SwoftEvent::TIMER_AFTER_AFTER)
 */
class AfterTimerAfterListener implements EventHandlerInterface
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