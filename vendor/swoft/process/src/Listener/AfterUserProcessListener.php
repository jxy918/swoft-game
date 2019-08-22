<?php declare(strict_types=1);


namespace Swoft\Process\Listener;


use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Process\ProcessEvent;
use Swoft\SwoftEvent;

/**
 * Class AfterUserProcessListener
 *
 * @since 2.0
 *
 * @Listener(event=ProcessEvent::AFTER_USER_PROCESS)
 */
class AfterUserProcessListener implements EventHandlerInterface
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