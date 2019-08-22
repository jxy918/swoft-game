<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\Rpc\Server\ServiceConnectContext;
use Swoft\Server\SwooleEvent;
use Swoft\Rpc\Server\ServiceServerEvent;

/**
 * Class BeforeConnectListener
 *
 * @since 2.0
 *
 * @Listener(event=ServiceServerEvent::BEFORE_CONNECT)
 */
class BeforeConnectListener implements EventHandlerInterface
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
        $context = ServiceConnectContext::new($server, $fd, $reactorId);

        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => SwooleEvent::CONNECT,
                'uri'         => (string)$fd,
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }
}
