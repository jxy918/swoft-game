<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Annotation\ServerListener;
use Swoft\Bean\Collector\ServerListenerCollector;

/**
 * Class ServerListenerParser
 * @package Swoft\Bean\Parser
 * @author inhere <in.798@qq.com>
 */
class ServerListenerParser extends AbstractParser
{
    /**
     * @param string      $className
     * @param ServerListener $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param mixed       $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        $beanName = $className;
        $scope    = Scope::SINGLETON;

        ServerListenerCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ''];
    }
}
