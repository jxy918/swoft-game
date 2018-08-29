<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\Bean\Annotation\SwooleListener;
use Swoft\Bootstrap\SwooleEvent;

/**
 * the collector of swoole listener
 *
 * @uses      SwooleListenerCollector
 * @version   2018年01月11日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class SwooleListenerCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $listeners;

    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param mixed  $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if (!($objectAnnotation instanceof SwooleListener)) {
            return;
        }

        $events = $objectAnnotation->getEvent();
        if (empty($events) || !\is_array($events)) {
            return;
        }

        $type = $objectAnnotation->getType();
        foreach ($events as $event) {
            if(!SwooleEvent::isSwooleEvent($event)){
                continue;
            }
            if($type === SwooleEvent::TYPE_PORT){
                $order = $objectAnnotation->getOrder();
                self::$listeners[$type][$order][$event] = $className;
            } elseif ($type === SwooleEvent::TYPE_SERVER){
                self::$listeners[$type][$event] = $className;
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
