<?php declare(strict_types=1);


namespace Swoft\Server\Listener;

use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\SwoftEvent;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Server\ServerEvent;

/**
 * Class AfterListener
 *
 * @since 2.0
 *
 * @Listener(event=ServerEvent::AFTER_EVENT)
 */
class AfterListener implements EventHandlerInterface
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