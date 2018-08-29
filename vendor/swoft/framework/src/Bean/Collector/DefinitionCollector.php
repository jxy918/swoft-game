<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\Bean\Annotation\Definition;

/**
 * The collector of definition
 */
class DefinitionCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $definitions = [];

    /**
     * collect
     *
     * @param string     $className
     * @param Definition $objectAnnotation
     * @param string     $propertyName
     * @param string     $methodName
     * @param null       $propertyValue
     *
     * @return void
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if ($objectAnnotation instanceof Definition) {

            $name = $objectAnnotation->getName();
            self::$definitions[$className] = empty($name) ? $className : $name;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$definitions;
    }
}