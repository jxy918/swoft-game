<?php declare(strict_types=1);

namespace SwoftTest\Console\Testing\Listener;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Console\CommandRegister;
use Swoft\Console\Router\Router;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\SwoftEvent;

/**
 * Class AppInitCompleteListener
 * @since 2.0
 *
 * @Listener(SwoftEvent::APP_INIT_COMPLETE)
 */
class AppInitCompleteListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        // - register console routes

        /** @var Router $router */
        $router = bean('cliRouter');

        CommandRegister::register($router);
    }
}
