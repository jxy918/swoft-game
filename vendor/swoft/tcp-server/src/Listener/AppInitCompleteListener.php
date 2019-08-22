<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Listener;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use Swoft\SwoftEvent;
use Swoft\Tcp\Server\Exception\TcpServerRouteException;
use Swoft\Tcp\Server\Router\Router;
use Swoft\Tcp\Server\Router\RouteRegister;
use function bean;

/**
 * Class AppInitCompleteListener
 *
 * @since 2.0
 * @Listener(SwoftEvent::APP_INIT_COMPLETE)
 */
class AppInitCompleteListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     * @throws TcpServerRouteException
     */
    public function handle(EventInterface $event): void
    {
        // Register tcp routes

        /** @var Router $router */
        $router = bean('tcpRouter');

        RouteRegister::registerTo($router);

        CLog::info('Tcp server route registered(routes %d)', $router->getCount());
    }
}
