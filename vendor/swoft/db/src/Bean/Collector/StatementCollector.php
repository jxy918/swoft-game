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
use Swoft\Db\Bean\Annotation\Statement;

/**
 * StatementCollector
 */
class StatementCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $statements = [];

    /**
     * @param string    $className
     * @param Statement $objectAnnotation
     * @param string    $propertyName
     * @param string    $methodName
     * @param null      $propertyValue
     *
     * @return void
     */
    public static function collect(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        if ($objectAnnotation instanceof Statement) {
            $driver                    = $objectAnnotation->getDriver();
            self::$statements[$driver] = $className;
        }
    }

    /**
     * @return array
     */
    public static function getCollector()
    {
        return self::$statements;
    }
}
