<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\Listener;
use Swoft\Bean\CollectorInterface;

/**
 * Collector listener
 */
class ListenerCollector implements CollectorInterface
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
        if($objectAnnotation instanceof Listener){
            $eventName = $objectAnnotation->getEvent();
            self::$listeners[$eventName][] = $className;
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