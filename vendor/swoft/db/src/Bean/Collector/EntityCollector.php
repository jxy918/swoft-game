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

use Swoft\Db\Bean\Annotation\Column;
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Bean\Annotation\Id;
use Swoft\Db\Bean\Annotation\Required;
use Swoft\Db\Bean\Annotation\Table;
use Swoft\Bean\CollectorInterface;

/**
 * The collector of entity
 */
class EntityCollector implements CollectorInterface
{
    /**
     * @var array
     */
    private static $entities = [];

    /**
     * @param string $className
     * @param null   $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null   $propertyValue
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
        if ($objectAnnotation instanceof Column) {
            self::collectColumn($objectAnnotation, $className, $propertyName, $propertyValue);
        } elseif ($objectAnnotation instanceof Entity) {
            self::collectEntity($objectAnnotation, $className);
        } elseif ($objectAnnotation instanceof Id) {
            self::collectId($className, $propertyName);
        } elseif ($objectAnnotation instanceof Required) {
            self::collectRequired($className, $propertyName);
        } elseif ($objectAnnotation instanceof Table) {
            self::collectTable($objectAnnotation, $className);
        }
    }

    /**
     * @param Table  $objectAnnotation
     * @param string $className
     */
    private static function collectTable(Table $objectAnnotation, string $className)
    {
        $tableName = $objectAnnotation->getName();

        self::$entities[$className]['table']['name'] = $tableName;
        self::$entities[$tableName] = $className;
    }

    /**
     * @param string $className
     * @param string $propertyName
     */
    private static function collectRequired(string $className, string $propertyName)
    {
        self::$entities[$className]['field'][$propertyName]['required'] = true;
    }

    /**
     * @param string $className
     * @param string $propertyName
     */
    private static function collectId(string $className, string $propertyName)
    {
        self::$entities[$className]['table']['id'] = $propertyName;
    }

    /**
     * @param Entity $objectAnnotation
     * @param string $className
     */
    private static function collectEntity(Entity $objectAnnotation, string $className)
    {
        $instance = $objectAnnotation->getInstance();
        self::$entities[$className]['instance'] = $instance;
    }

    /**
     * @param Column $objectAnnotation
     * @param string $className
     * @param string $propertyName
     * @param mixed  $propertyValue
     */
    private static function collectColumn(
        Column $objectAnnotation,
        string $className,
        string $propertyName,
        $propertyValue
    ) {
        $columnName = $objectAnnotation->getName();

        $entity                                             = [
            'type'    => $objectAnnotation->getType(),
            'length'  => $objectAnnotation->getLength(),
            'column'  => $columnName,
            'default' => $propertyValue,
        ];
        self::$entities[$className]['field'][$propertyName] = $entity;
        self::$entities[$className]['column'][$columnName]  = $propertyName;
    }

    /**
     * @return array
     */
    public static function getCollector(): array
    {
        return self::$entities;
    }
}
