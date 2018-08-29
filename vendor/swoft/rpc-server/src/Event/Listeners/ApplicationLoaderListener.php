<?php

namespace Swoft\Rpc\Server\Event\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Rpc\Server\Bean\Collector\ServiceCollector;

/**
 * The listener of applicatioin loader
 * @Listener(AppEvent::APPLICATION_LOADER)
 */
class ApplicationLoaderListener implements EventHandlerInterface
{
    /**
     * @param \Swoft\Event\EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        /* @var \Swoft\Rpc\Server\Router\HandlerMapping $serviceRouter */
        $serviceRouter = App::getBean('serviceRouter');

        $serviceMapping = ServiceCollector::getCollector();
        $serviceRouter->register($serviceMapping);
    }
}