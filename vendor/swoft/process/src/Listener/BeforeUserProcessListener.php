<?php declare(strict_types=1);


namespace Swoft\Process\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\Process\Context\UserProcessContext;
use Swoft\Process\ProcessEvent;

/**
 * Class BeforeUserProcessListener
 *
 * @since 2.0
 *
 * @Listener(event=ProcessEvent::BEFORE_USER_PROCESS)
 */
class BeforeUserProcessListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        [$server, $process, $name] = $event->getParams();

        $context = UserProcessContext::new($server, $process);
        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => sprintf('swoft.process.user.%s', (string)$name),
                'uri'         => '',
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }
}