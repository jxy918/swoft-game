<?php

namespace Swoft\Process\Bean\Collector;

use Swoft\Process\Bean\Annotation\Process;
use Swoft\Bean\CollectorInterface;

/**
 * The process collector
 */
class ProcessCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $processes = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     *
     * @return void
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if ($objectAnnotation instanceof Process) {

            $num       = $objectAnnotation->getNum();
            $name      = $objectAnnotation->getName();
            $boot      = $objectAnnotation->isBoot();
            $inout     = $objectAnnotation->isInout();
            $pipe      = $objectAnnotation->isPipe();
            $coroutine = $objectAnnotation->isCoroutine();
            $name      = empty($name) ? $className : $name;

            self::$processes[$name] = [
                'name'  => $name,
                'num'   => $num,
                'boot'  => $boot,
                'pipe'  => $pipe,
                'inout' => $inout,
                'co'    => $coroutine,
                'class' => $className,
            ];

            return;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$processes;
    }

}