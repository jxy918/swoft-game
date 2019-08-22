<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Listener;

use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use Swoft\SwoftEvent;
use Swoft\WebSocket\Server\Router\Router;
use Swoft\WebSocket\Server\Router\RouteRegister;
use function bean;

/**
 * Class AppInitCompleteListener
 *
 * @since 2.0
 *
 * @Listener(SwoftEvent::APP_INIT_COMPLETE)
 */
class AppInitCompleteListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {
        // Register WebSocket routes

        /** @var Router $router */
        $router = bean('wsRouter');

        RouteRegister::registerTo($router);

        CLog::info('WebSocket server route registered(module %d, message command %d)', $router->getModuleCount(), $router->getCounter());
    }
}
