<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Db\Helper;

use Swoft\Db\Bean\Collector\EntityCollector;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Types;
use Swoft\Helper\StringHelper;

/**
 * EntityHelper
 */
class EntityHelper
{
    /**
     * @param array  $result
     * @param string $className
     *
     * @return array
     */
    public static function formatListByType(array $result, string $className): array
    {
        if (empty($result)) {
            return [];
        }

        $rowList = [];
        foreach ($result as $row) {
            $rowList[] = self::formatRowByType($row, $className);
        }

        return $rowList;
    }

    /**
     * @param array  $row
     * @param string $className
     *
     * @return array
     */
    public static function formatRowByType(array $row, string $className): array
    {
        $rowAry   = [];
        $entities = EntityCollector::getCollector();
        if (!isset($entities[$className])) {
            return $row;
        }

        if (strpos($className, '\\') === false) {
            $className = $entities[$className];
        }
        foreach ($row as $name => $value) {
            $field = $entities[$className]['column'][$name];
            $type  = $entities[$className]['field'][$field]['type'];

            $rowAry[$name] = self::trasferTypes($type, $value);
        }

        return $rowAry;
    }

    /**
     * @param array  $result
     * @param string $className
     *
     * @return array
     */
    public static function listToEntity(array $result, string $className): array
    {
        $entities = [];
        foreach ($result as $data) {
            if (!\is_array($data)) {
                continue;
            }
            $entities[] = self::arrayToEntity($data, $className);
        }

        return $entities;
    }

    /**
     * @param array  $data
     * @param string $className
     *
     * @return object
     */
    public static function arrayToEntity(array $data, string $className)
    {
        $attrs    = [];
        $object   = new $className();
        $entities = EntityCollector::getCollector();

        foreach ($data as $col => $value) {
            if (!isset($entities[$className]['column'][$col])) {
                continue;
            }

            $field        = $entities[$className]['column'][$col];
            $setterMethod = StringHelper::camel('set_' . $field);

            $type  = $entities[$className]['field'][$field]['type'];
            $value = self::trasferTypes($type, $value);

            if (\method_exists($object, $setterMethod)) {
                $attrs[$field] = $value;
                // 当从数据库取出来的数据不为null时，再进行模型赋值
                if ($value !== null) {
                    $object->$setterMethod($value);
                }
            }
        }
        if (\method_exists($object, 'setAttrs')) {
            $object->setAttrs($attrs);
        }

        return $object;
    }

    /**
     * @param $type
     * @param $value
     *
     * @return bool|float|int|string
     */
    public static function trasferTypes($type, $value)
    {
        if ($value === null) {
            $value = null;
        } elseif ($type === Types::INT || $type === Types::NUMBER) {
            $value = (int)$value;
        } elseif ($type === Types::STRING) {
            $value = null === $value ? null : (string)$value;
        } elseif ($type === Types::BOOLEAN) {
            $value = (bool)$value;
        } elseif ($type === Types::FLOAT) {
            $value = (float)$value;
        }

        return $value;
    }

    /**
     * @param mixed  $key
     * @param mixed  $value
     * @param string $type
     *
     * @throws DbException
     *
     * @return array
     */
    public static function transferParameter($key, $value, $type): array
    {
        if (!\is_int($key) && !\is_string($key)) {
            throw new DbException('Key must to be int Or string! ');
        }
        $key = self::formatParamsKey($key);

        // 参数值类型转换
        if ($type !== null) {
            $value = self::trasferTypes($type, $value);
        }

        return [$key, $value];
    }

    /**
     * @param mixed $key
     *
     * @return string
     */
    private static function formatParamsKey($key): string
    {
        if (\is_string($key) && \strpos($key, ':') === false) {
            return ':' . $key;
        }

        return $key;
    }
}
