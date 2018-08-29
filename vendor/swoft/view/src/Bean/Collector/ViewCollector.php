<?php

namespace Swoft\View\Bean\Collector;

use Swoft\App;
use Swoft\Bean\CollectorInterface;
use Swoft\View\Bean\Annotation\View;

/**
 * the collector of view
 *
 * @uses      ViewCollector
 * @version   2018年01月15日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ViewCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $views = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @throws \InvalidArgumentException
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        if ($objectAnnotation instanceof View) {
            self::$views[$className]['view'][$methodName] = [
                'template' => App::getAlias($objectAnnotation->getTemplate()),
                'layout'   => App::getAlias($objectAnnotation->getLayout()),
            ];
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$views;
    }

}
