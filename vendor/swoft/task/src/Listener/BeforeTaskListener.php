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
use Swoft\Task\Request;
use Swoft\Task\Response;
use Swoft\Task\TaskContext;
use Swoft\Task\TaskEvent;

/**
 * Class BeforeTaskListener
 *
 * @since 2.0
 *
 * @Listener(event=TaskEvent::BEFORE_TASK)
 */
class BeforeTaskListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        /**
         * @var Request  $request
         * @var Response $response
         */
        [$request, $response] = $event->getParams();
        $context = TaskContext::new($request, $response);

        if (Log::getLogger()->isEnable()) {
            $uri  = sprintf('%s::%s', $request->getName(), $request->getMethod());
            $data = [
                'event'       => SwooleEvent::TASK,
                'uri'         => $uri,
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }
        Context::set($context);
    }
}
