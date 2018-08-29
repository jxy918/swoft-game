<?php

namespace Swoft\Sg\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\Sg\Bean\Annotation\Fallback;

/**
 * Fallback collector
 */
class FallbackCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $fallbacks = [];

    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @return void
     */
    public static function collect(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        if($objectAnnotation instanceof Fallback){
            $name = $objectAnnotation->getName();
            $fallbackName = empty($name)?$className:$name;
            self::$fallbacks[$fallbackName] = $className;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$fallbacks;
    }
}