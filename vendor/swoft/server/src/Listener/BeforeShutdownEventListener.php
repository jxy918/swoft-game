<?php declare(strict_types=1);


namespace Swoft\Server\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\Server\Context\ShutdownContext;
use Swoft\Server\ServerEvent;
use Swoft\Server\SwooleEvent;

/**
 * Class BeforeShutdownEventListener
 *
 * @since 2.0
 *
 * @Listener(event=ServerEvent::BEFORE_SHUTDOWN_EVENT)
 */
class BeforeShutdownEventListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        [$server] = $event->getParams();
        $context = ShutdownContext::new($server);

        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => SwooleEvent::SHUTDOWN,
                'uri'         => '',
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }
}