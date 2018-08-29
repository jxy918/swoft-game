<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Bean\Collector;

use Swoft\Bean\CollectorInterface;
use Swoft\Db\Bean\Annotation\Connection;

/**
 * The collector of connect
 */
class ConnectionCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $connects = [];

    /**
     * Do collect
     *
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
     * @return void
     */
    public static function collect(string $className, $objectAnnotation = null, string $propertyName = '', string $methodName = '', $propertyValue = null)
    {
        if ($objectAnnotation instanceof Connection) {
            $type   = $objectAnnotation->getType();
            $driver = $objectAnnotation->getDriver();
            self::$connects[$driver][$type] = $className;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$connects;
    }
}
