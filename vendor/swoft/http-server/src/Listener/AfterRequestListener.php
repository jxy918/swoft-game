<?php declare(strict_types=1);


namespace Swoft\Http\Server\Listener;


use Exception;
use Swoft;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\HttpServerEvent;
use Swoft\SwoftEvent;

/**
 * Class AfterRequestListener
 *
 * @since 2.0
 *
 * @Listener(HttpServerEvent::AFTER_REQUEST)
 */
class AfterRequestListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws Exception
     */
    public function handle(EventInterface $event): void
    {
        /** @var Response $response */
        $response = $event->getParam(0);
        $response->send();

        // Defer
        Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

        // Destroy
        Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);
    }
}
