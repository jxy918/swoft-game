<?php

namespace Swoft\WebSocket\Server\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\WebSocket\Server\Bean\Annotation\WebSocket;

/**
 * Class WsCollector
 * @package Swoft\View\Bean\Collector
 */
class WebSocketCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $controllers = [];

    /**
     * @param string $className
     * @param WebSocket $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        if ($objectAnnotation instanceof WebSocket) {
            $path = $objectAnnotation->getPath();

            self::$controllers[$path] = [
                'path' => $objectAnnotation->getPath(),
                'handler' => $className,
            ];
        }
    }

    /**
     * @return array
     */
    public static function getCollector(): array
    {
        return self::$controllers;
    }
}
