<?php

namespace Swoft\Console\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\Console\Bean\Annotation\Command;
use Swoft\Console\Bean\Annotation\Mapping;

/**
 * Command Collector
 */
class CommandCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $commandMapping = [];

    /**
     * collect
     *
     * @param string $className
     * @param mixed $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        if ($objectAnnotation instanceof Command) {
            self::collectCommand($className, $objectAnnotation);
        } elseif ($objectAnnotation instanceof Mapping) {
            self::collectMapping($className, $objectAnnotation, $methodName);
        } elseif ($objectAnnotation === null && isset(self::$commandMapping[$className])) {
            self::collectWithoutAnnotation($className, $methodName);
        }
    }

    /**
     * collect command
     *
     * @param string  $className
     * @param Command $objectAnnotation
     */
    private static function collectCommand(string $className, Command $objectAnnotation)
    {
        $commandName = $objectAnnotation->getName();
        $coroutine = $objectAnnotation->isCoroutine();
        $server = $objectAnnotation->isServer();

        self::$commandMapping[$className]['name'] = $commandName;
        self::$commandMapping[$className]['enabled'] = $objectAnnotation->isEnabled();
        self::$commandMapping[$className]['coroutine'] = $coroutine;
        self::$commandMapping[$className]['server'] = $server;
    }

    /**
     * collect mapping
     *
     * @param string  $className
     * @param Mapping $objectAnnotation
     * @param string  $methodName
     */
    private static function collectMapping(string $className, Mapping $objectAnnotation, string $methodName)
    {
        $mapped = $objectAnnotation->getName();

        self::$commandMapping[$className]['routes'][] = [
            'mappedName' => $mapped,
            'methodName' => $methodName,
        ];
    }

    /**
     * @param string $className
     * @param string $methodName
     */
    private static function collectWithoutAnnotation(string $className, string $methodName)
    {
        self::$commandMapping[$className]['routes'][] = [
            'mappedName' => '',
            'methodName' => $methodName,
        ];
    }

    /**
     * @return array
     */
    public static function getCollector(): array
    {
        return self::$commandMapping;
    }
}
