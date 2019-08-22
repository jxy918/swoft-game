<?php declare(strict_types=1);


namespace Swoft\Server\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\Server\Context\WorkerStartContext;
use Swoft\Server\Context\WorkerStopContext;
use Swoft\Server\ServerEvent;
use Swoft\Server\SwooleEvent;

/**
 * Class BeforeWorkerStopListener
 *
 * @since 2.0
 *
 * @Listener(event=ServerEvent::BEFORE_WORKER_STOP_EVENT)
 */
class BeforeWorkerStopListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        [$server, $workerId] = $event->getParams();
        $context = WorkerStopContext::new($server, $workerId);

        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => SwooleEvent::WORKER_STOP,
                'uri'         => (string)$workerId,
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }

}