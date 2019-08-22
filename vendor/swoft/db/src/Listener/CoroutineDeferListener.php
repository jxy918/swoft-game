<?php declare(strict_types=1);


namespace Swoft\Db\Listener;

use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Connection\ConnectionManager;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\SwoftEvent;
use Throwable;

/**
 * Class CoroutineDeferListener
 *
 * @since 2.0
 *
 * @Listener(SwoftEvent::COROUTINE_DEFER)
 */
class CoroutineDeferListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function handle(EventInterface $event): void
    {
        /* @var ConnectionManager $cm*/
        $cm = bean(ConnectionManager::class);
        $cm->release();
    }
}
