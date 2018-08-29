<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\Pool;
use Swoft\Bean\CollectorInterface;

/**
 * the collector of pool
 *
 * @uses      PoolCollector
 * @version   2018年01月14日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class PoolCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $pools = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if($objectAnnotation instanceof Pool){
            $poolName = $objectAnnotation->getName();
            $poolName = empty($poolName) ? $className : $poolName;
            self::$pools[$poolName] = $className;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$pools;
    }

}