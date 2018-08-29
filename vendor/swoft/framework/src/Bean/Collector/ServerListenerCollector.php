<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bean\Annotation\ServerListener;
use Swoft\Bean\CollectorInterface;
use Swoft\Bootstrap\SwooleEvent;

/**
 * Server listener
 */
class ServerListenerCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $listeners = [];

    /**
     * @param string $className
     * @param object   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @return void
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if($objectAnnotation instanceof BeforeStart){
            self::$listeners[SwooleEvent::ON_BEFORE_START][] = $className;
        } elseif($objectAnnotation instanceof ServerListener){
            $events = $objectAnnotation->getEvent();

            foreach ($events as $event) {
                self::$listeners[$event][] = $className;
            }
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$listeners;
    }

}
