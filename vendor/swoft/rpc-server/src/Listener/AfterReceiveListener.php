<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Listener;


use ReflectionException;
use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Rpc\Exception\RpcException;
use Swoft\Rpc\Server\Response;
use Swoft\SwoftEvent;
use Swoft\Rpc\Server\ServiceServerEvent;

/**
 * Class AfterReceiveListener
 *
 * @since 2.0
 *
 * @Listener(event=ServiceServerEvent::AFTER_RECEIVE)
 */
class AfterReceiveListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     * @throws RpcException
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