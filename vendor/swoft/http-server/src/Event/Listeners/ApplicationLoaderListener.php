<?php

namespace Swoft\Http\Server\Event\Listeners;
use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Http\Server\Bean\Collector\ControllerCollector;

/**
 * the listener of application loader
 *
 * @Listener(AppEvent::APPLICATION_LOADER)
 * @uses      ApplicationLoaderListener
 * @version   2018年01月08日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ApplicationLoaderListener implements EventHandlerInterface
{
    /**
     * @param \Swoft\Event\EventInterface $event
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function handle(EventInterface $event)
    {
        /* @var \Swoft\Http\Server\Router\HandlerMapping $httpRouter */
        $httpRouter = App::getBean('httpRouter');

        $requestMapping = ControllerCollector::getCollector();
        $httpRouter->registerRoutes($requestMapping);
    }
}
