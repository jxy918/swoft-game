<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\Handler;
use Swoft\Bean\CollectorInterface;

/**
 * the collector of exception handler
 *
 * @uses      ExceptionHandlerCollector
 * @version   2018年01月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ExceptionHandlerCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $handlers = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if ($objectAnnotation instanceof Handler) {
            $exceptionClass = $objectAnnotation->getException();
            self::$handlers[$exceptionClass] = [
                $className,
                $methodName
            ];
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$handlers;
    }

}