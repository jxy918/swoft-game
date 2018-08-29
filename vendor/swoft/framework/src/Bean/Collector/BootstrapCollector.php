<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\Bean\Annotation\Bootstrap;

/**
 * the collector of bootstrap
 *
 * @uses      BootstrapCollector
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BootstrapCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $bootstraps = [];

    /**
     * collect
     *
     * @param string    $className
     * @param Bootstrap $objectAnnotation
     * @param string    $propertyName
     * @param string    $methodName
     * @param null      $propertyValue
     * @return void
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        if ($objectAnnotation instanceof Bootstrap) {

            $name = $objectAnnotation->getName();
            $order = $objectAnnotation->getOrder();

            self::$bootstraps[$className]['name'] = $name;
            self::$bootstraps[$className]['order'] = $order;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$bootstraps;
    }

}
