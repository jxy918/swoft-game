<?php

namespace Swoft\WebSocket\Server\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Bean\Annotation\Scope;
use Swoft\WebSocket\Server\Bean\Collector\WebSocketCollector;
use Swoft\WebSocket\Server\Bean\Annotation\WebSocket;

/**
 * Class WebSocketParser
 * @package Swoft\Http\Server\Bean\Parser
 */
class WebSocketParser extends AbstractParser
{
    /**
     * WebSocket Controller注解解析
     *
     * @param string $className
     * @param WebSocket $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param string|null $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null): array
    {
        $beanName = $className;
        $scope = Scope::SINGLETON;

        // collect controller
        WebSocketCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ''];
    }
}
