<?php declare(strict_types=1);


namespace Swoft\Task\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\Server\SwooleEvent;
use Swoft\Task\FinishContext;
use Swoft\Task\TaskEvent;

/**
 * Class BeforeFinishListener
 *
 * @since 2.0
 *
 * @Listener(TaskEvent::BEFORE_FINISH)
 */
class BeforeFinishListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        list($server, $taskId, $data) = $event->getParams();

        $context = FinishContext::new($server, $taskId, $data);

        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => SwooleEvent::FINISH,
                'uri'         => $context->getTaskUniqid(),
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }
}
