<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Listener;


use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Rpc\Server\ServiceServerEvent;
use Swoft\SwoftEvent;


/**
 * Class AfterConnectListener
 *
 * @since 2.0
 *
 * @Listener(ServiceServerEvent::AFTER_CONNECT)
 */
class AfterConnectListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        // Defer
        Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

        // Destroy
        Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);
    }
}