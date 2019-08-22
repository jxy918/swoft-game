<?php declare(strict_types=1);


namespace Swoft\Task\Listener;


use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\SwoftEvent;
use Swoft\Task\Response;
use Swoft\Task\TaskEvent;

/**
 * Class AfterTaskListener
 *
 * @since 2.0
 *
 * @Listener(event=TaskEvent::AFTER_TASK)
 */
class AfterTaskListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        /* @var Response $response */
        $response = $event->getParam(0);
        $response->send();

        // Defer
        Swoft::trigger(SwoftEvent::COROUTINE_DEFER);
        // Destroy
        Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);
    }
}