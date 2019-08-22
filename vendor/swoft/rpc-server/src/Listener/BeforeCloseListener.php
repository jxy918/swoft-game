<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\Rpc\Server\ServiceCloseContext;
use Swoft\Server\SwooleEvent;
use Swoft\Rpc\Server\ServiceServerEvent;

/**
 * Class BeforeCloseListener
 *
 * @since 2.0
 *
 * @Listener(event=ServiceServerEvent::BEFORE_CLOSE)
 */
class BeforeCloseListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        list($server, $fd, $reactorId) = $event->getParams();
        $context = ServiceCloseContext::new($server, $fd, $reactorId);

        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => SwooleEvent::CLOSE,
                'uri'         => (string)$fd,
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }
}
