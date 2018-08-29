<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Collector\ServerListenerCollector;

/**
 * the parser of before start
 *
 * @uses      BeforeStartParser
 * @version   2018年01月13日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BeforeStartParser extends AbstractParser
{
    /**
     * @param string      $className
     * @param BeforeStart $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param mixed       $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $beanName = $className;
        $scope    = Scope::SINGLETON;

        ServerListenerCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ""];
    }
}