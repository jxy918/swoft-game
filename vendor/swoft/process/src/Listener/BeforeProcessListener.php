<?php declare(strict_types=1);


namespace Swoft\Process\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\Process\Context\ProcessContext;
use Swoft\Process\ProcessEvent;

/**
 * Class BeforeProcessListener
 *
 * @since 2.0
 *
 * @Listener(event=ProcessEvent::BEFORE_PROCESS)
 */
class BeforeProcessListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        var_dump('BeforeProcessListener');
        [$pool, $workerId] = $event->getParams();

        $context = ProcessContext::new($pool, $workerId);
        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => 'swoft.process.worker.start',
                'uri'         => '',
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }
}