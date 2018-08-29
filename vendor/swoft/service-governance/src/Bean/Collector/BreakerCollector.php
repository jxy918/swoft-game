<?php

namespace Swoft\Sg\Bean\Collector;

use Swoft\Sg\Bean\Annotation\Breaker;
use Swoft\Bean\CollectorInterface;

/**
 * the collector of breaker
 */
class BreakerCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $breakers = [];

    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     *
     * @return void
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if ($objectAnnotation instanceof Breaker) {
            $breakerName = $objectAnnotation->getName();
            $breakerName = empty($breakerName) ? $className : $breakerName;

            self::$breakers[$breakerName] = $className;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$breakers;
    }

}