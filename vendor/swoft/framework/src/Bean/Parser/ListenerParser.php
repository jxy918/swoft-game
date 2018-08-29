<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Listener;
use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Collector\ListenerCollector;

/**
 * Listen注解解析器
 *
 * @uses      ListenerParser
 * @version   2017年09月03日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ListenerParser extends AbstractParser
{
    /**
     * Listen注解解析
     *
     * @param string   $className
     * @param Listener $objectAnnotation
     * @param string   $propertyName
     * @param string   $methodName
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $beanName = $className;
        $scope = Scope::SINGLETON;
        ListenerCollector::collect($className, $objectAnnotation, $propertyName,$methodName, $propertyValue);
        return [$beanName, $scope, ""];
    }
}
