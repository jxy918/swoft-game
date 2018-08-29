<?php

namespace Swoft\Bean\Collector;

use Swoft\Bean\Annotation\BootBean;
use Swoft\Bean\CollectorInterface;

/**
 * The collector of boot bean
 */
class BootBeanCollector implements CollectorInterface
{
    /**
     * The type of server
     */
    const TYPE_SERVER = 'server';

    /**
     * The type of worker
     */
    const TYPE_WORKER = 'worker';

    /**
     * @var array
     */
    private static $beans = [];

    /**
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        if ($objectAnnotation instanceof BootBean) {
            $server = $objectAnnotation->isServer();
            if($server){
                self::$beans[self::TYPE_SERVER][] = $className;
            }else{
                self::$beans[self::TYPE_WORKER][] = $className;
            }
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$beans;
    }
}