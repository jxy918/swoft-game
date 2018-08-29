<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Db;

use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Core\ResultInterface;
use Swoft\Db\Bean\Collector\EntityCollector;
use Swoft\Db\Exception\MysqlException;
use Swoft\Db\Validator\ValidatorInterface;
use Swoft\Exception\ValidatorException;
use Swoft\Helper\StringHelper;

/**
 * Executor
 */
class Executor
{
    /**
     * @param object $entity
     *
     * @return ResultInterface
     */
    public static function save($entity): ResultInterface
    {
        $className = \get_class($entity);
        list($table, , , $fields) = self::getFields($entity, 1);
        $instance = self::getInstance($className);

        $fields = $fields ?? [];
        $query  = Query::table($table)->selectInstance($instance);
        // Set Primary Id to Entity
        $query->addDecorator(function ($primaryId) use ($entity, $className) {
            list(, , $idColumn) = self::getTable($className);
            $setter = 'set' . StringHelper::camel($idColumn, false);
            method_exists($entity, $setter) && $entity->$setter($primaryId);
            return $primaryId;
        });
        return $query->insert($fields);
    }

    /**
     * @param string $className
     * @param array  $rows
     *
     * @return ResultInterface
     */
    public static function batchInsert(string $className, array $rows): ResultInterface
    {
        $instance = self::getInstance($className);

        return Query::table($className)->selectInstance($instance)->batchInsert($rows);
    }

    /**
     * @param object $entity
     *
     * @return ResultInterface
     */
    public static function delete($entity): ResultInterface
    {
        $className = \get_class($entity);
        list($table, , , $fields) = self::getFields($entity, 3);
        $instance = self::getInstance($className);

        $query = Query::table($table)->selectInstance($instance);
        foreach ($fields ?? [] as $column => $value) {
            $query->where($column, $value);
        }

        return $query->delete();
    }

    /**
     * @param string $className
     * @param mixed  $id
     *
     * @return ResultInterface
     */
    public static function deleteById($className, $id): ResultInterface
    {
        list($table, , $idColumn) = self::getTable($className);
        $instance = self::getInstance($className);

        $query = Query::table($table)->where($idColumn, $id)->selectInstance($instance);

        return $query->delete();
    }

    /**
     * @param string $className
     * @param array  $ids
     *
     * @return ResultInterface
     */
    public static function deleteByIds($className, array $ids): ResultInterface
    {
        list($table, , $idColumn) = self::getTable($className);
        $instance = self::getInstance($className);

        $query = Query::table($table)->whereIn($idColumn, $ids)->selectInstance($instance);

        return $query->delete();
    }

    /**
     * @param string $className
     * @param array  $condition
     *
     * @return ResultInterface
     */
    public static function deleteOne(string $className, array $condition)
    {
        $instance = self::getInstance($className);

        return Query::table($className)->selectInstance($instance)->condition($condition)->limit(1)->delete();
    }

    /**
     * @param string $className
     * @param array  $condition
     *
     * @return ResultInterface
     */
    public static function deleteAll(string $className, array $condition)
    {
        $instance = self::getInstance($className);

        return Query::table($className)->selectInstance($instance)->condition($condition)->delete();
    }

    /**
     * @param object $entity
     *
     * @return ResultInterface
     */
    public static function update($entity): ResultInterface
    {
        $className = \get_class($entity);
        list($table, $idColumn, $idValue, $fields) = self::getFields($entity, 2);

        if (empty($fields)) {
            return new DbDataResult(0);
        }
        // 构建update查询器
        $instance = self::getInstance($className);
        $fields   = $fields ?? [];
        $query    = Query::table($table)->className($className)->where($idColumn, $idValue)->selectInstance($instance);

        return $query->update($fields);
    }

    /**
     * @param string $className
     * @param array  $attributes
     * @param array  $condition
     *
     * @return ResultInterface
     */
    public static function updateOne(string $className, array $attributes, array $condition)
    {
        $instance = self::getInstance($className);

        return Query::table($className)->selectInstance($instance)->condition($condition)->limit(1)->update($attributes);
    }

    /**
     * @param string $className
     * @param array  $attributes
     * @param array  $condition
     *
     * @return ResultInterface
     */
    public static function updateAll(string $className, array $attributes, array $condition)
    {
        $instance = self::getInstance($className);

        return Query::table($className)->selectInstance($instance)->condition($condition)->update($attributes);
    }

    /**
     * @param object $entity
     *
     * @return ResultInterface
     */
    public static function find($entity): ResultInterface
    {
        $className = \get_class($entity);
        list($tableName, , , $fields) = self::getFields($entity, 3);
        $instance = self::getInstance($className);

        $query = Query::table($tableName)->className($className)->selectInstance($instance);
        foreach ($fields ?? [] as $column => $value) {
            $query->where($column, $value);
        }

        return $query->get();
    }

    /**
     * @param string $className
     * @param mixed  $id
     *
     * @return ResultInterface
     */
    public static function exist(string $className, $id): ResultInterface
    {
        list($tableName, , $idColumn) = self::getTable($className);
        $instance = self::getInstance($className);
        $query    = Query::table($tableName)->where($idColumn, $id)->limit(1)->selectInstance($instance)->addDecorator(function ($result) {
            return (bool)$result;
        });

        return $query->get([$idColumn]);
    }

    /**
     * @param string $className
     * @param string $column
     * @param array  $condition
     *
     * @return ResultInterface
     */
    public static function count(string $className, string $column, array $condition): ResultInterface
    {
        $instance = self::getInstance($className);
        $query    = Query::table($className)->selectInstance($instance)->condition($condition);

        return $query->count($column);
    }

    /**
     * @param string $className
     * @param mixed  $id
     * @param array  $options
     *
     * @return ResultInterface
     */
    public static function findById($className, $id, array $options): ResultInterface
    {
        list($tableName, , $columnId) = self::getTable($className);
        $instance = self::getInstance($className);

        $query = Query::table($tableName)->className($className)->where($columnId, $id)->selectInstance($instance)->addDecorator(function ($result) {
            if (isset($result[0])) {
                return $result[0];
            }

            return null;
        });

        $options['limit'] = 1;
        $query            = self::applyOptions($query, $options);
        $fields           = self::getFieldsFromOptions($options);

        return $query->get($fields);
    }

    /**
     * @param string $className
     * @param array  $ids
     * @param array  $options
     *
     * @return ResultInterface
     */
    public static function findByIds($className, array $ids, array $options): ResultInterface
    {
        list($tableName, , $columnId) = self::getTable($className);
        $instance = self::getInstance($className);

        $query  = Query::table($tableName)->className($className)->whereIn($columnId, $ids)->selectInstance($instance);
        $query  = self::applyOptions($query, $options);
        $fields = self::getFieldsFromOptions($options);

        return $query->get($fields);
    }

    /**
     * @param string $className
     * @param array  $condition
     * @param array  $options
     *
     * @return \Swoft\Core\ResultInterface
     */
    public static function findOne(string $className, array $condition = [], array $options = [])
    {
        $instance = self::getInstance($className);
        $query    = Query::table($className)->className($className)->selectInstance($instance)->addDecorator(function ($result) {
            if (isset($result[0])) {
                return $result[0];
            }

            return null;
        });

        if (!empty($condition)) {
            $query = $query->condition($condition);
        }

        $options['limit'] = 1;
        $query            = self::applyOptions($query, $options);
        $fields           = self::getFieldsFromOptions($options);

        return $query->get($fields);
    }

    /**
     * @param string $className
     * @param array  $condition
     * @param array  $options
     *
     * @return ResultInterface
     */
    public static function findAll(string $className, array $condition = [], array $options = [])
    {
        $instance = self::getInstance($className);
        $query    = Query::table($className)->className($className)->selectInstance($instance);

        if (!empty($condition)) {
            $query = $query->condition($condition);
        }

        $query  = self::applyOptions($query, $options);
        $fields = self::getFieldsFromOptions($options);

        return $query->get($fields);
    }

    /**
     * @param string $className
     * @param array  $counters
     * @param array  $condition
     *
     * @return ResultInterface
     */
    public static function counter(string $className, array $counters, array $condition = [])
    {
        $instance = self::getInstance($className);
        $query    = Query::table($className)->className($className)->selectInstance($instance);

        if (!empty($condition)) {
            $query = $query->condition($condition);
        }

        return $query->counter($counters);
    }

    /**
     * @param string $className
     *
     * @return QueryBuilder
     */
    public static function query(string $className): QueryBuilder
    {
        $instance = self::getInstance($className);

        return Query::table($className)->className($className)->selectInstance($instance);
    }

    /**
     * @param object $entity 实体对象
     * @param int    $type   类型，1=insert 3=delete|find 2=update
     *
     * @return array
     * @throws \Swoft\Exception\ValidatorException
     */
    private static function getFields($entity, $type = 1): array
    {
        $changeFields = [];

        // 实体表结构信息
        list($table, $id, $idColumn, $fields) = self::getClassMetaData($entity);

        // 实体映射字段、值处理以及验证处理
        $idValue = null;
        foreach ($fields as $proName => $proAry) {
            $column  = $proAry['column'];
            $default = $proAry['default'];

            // 实体属性对应值
            $proValue = self::getEntityProValue($entity, $proName);

            self::validate($proAry, $proValue);

            if($type === 1 && $proValue === null){
                continue;
            }

            // insert逻辑
            if ($type === 1 && $id === $proName && $default === $proValue) {
                continue;
            }

            // update逻辑
            if ($type === 2 && null === $proValue) {
                continue;
            }

            // delete和find逻辑
            if ($type === 3 && $default === $proValue) {
                continue;
            }

            // id值赋值
            if ($idColumn === $column) {
                $idValue = $proValue;
            }

            $changeFields[$column] = $proValue;
        }

        // 如果是更新找到变化的字段
        if ($type === 2) {
            $oldFields    = $entity->getAttrs();
            $oldFields    = self::getDbOldFields(get_class($entity), $oldFields);
            $changeFields = self::getUpdateFields($oldFields, $changeFields);
        }
        return [$table, $idColumn, $idValue, $changeFields];
    }

    /**
     * @param string $className
     * @param array  $oldFields
     *
     * @return array
     */
    public static function getDbOldFields(string $className, array $oldFields): array
    {
        $fields       = [];
        $entities     = EntityCollector::getCollector();
        $entityfields = $entities[$className]['field'];
        foreach ($oldFields as $fieldName => $value) {
            if (isset($entityfields[$fieldName]) && $entityfields[$fieldName] != $fieldName) {
                $fieldName = $entityfields[$fieldName]['column'];
            }
            $fields[$fieldName] = $value;
        }

        return $fields;
    }

    /**
     * @param array $oldFields
     * @param array $changeFields
     *
     * @return array
     */
    private static function getUpdateFields(array $oldFields, array $changeFields)
    {
        foreach ($oldFields as $fieldName => $fieldValue) {
            if (!isset($changeFields[$fieldName])) {
                continue;
            }

            $changeValue = $changeFields[$fieldName];
            if ($changeValue == $fieldValue) {
                unset($changeFields[$fieldName]);
                continue;
            }
        }

        return $changeFields;
    }

    /**
     * 属性值验证
     *
     * @param array $columnAry     属性字段验证规则
     * @param mixed $propertyValue 数组字段值
     *
     * @throws MysqlException
     */
    private static function validate(array $columnAry, $propertyValue)
    {
        // 验证信息
        $column    = $columnAry['column'];
        $length    = $columnAry['length'] ?? -1;
        $validates = $columnAry['validates'] ?? [];
        $type      = $columnAry['type'] ?? Types::STRING;
        $required  = $columnAry['required'] ?? false;

        // 必须传值验证
        if ($propertyValue === null && $required) {
            throw new MysqlException(sprintf('The %s must pass values', $column));
        }

        if($propertyValue === null){
            return ;
        }

        // 类型验证器
        $validator = [
            'name'  => ucfirst($type),
            'value' => [$length],
        ];

        // 所有验证器
        array_unshift($validates, $validator);

        // 循环验证，一个验证不通过，验证失败
        foreach ($validates as $validate) {
            $name     = $validate['name'];
            $params   = $validate['value'];
            $beanName = 'Db' . $name . 'Validator';

            // 验证器未定义
            if (!BeanFactory::hasBean($beanName)) {
                App::warning('验证器不存在，beanName=' . $beanName);
                continue;
            }

            /* @var ValidatorInterface $objValidator */
            $objValidator = App::getBean($beanName);
            $objValidator->validate($column, $propertyValue, $params);
        }
    }

    /**
     * 实体属性对应的值
     *
     * @param object $entity  实体对象
     * @param string $proName 属性名称
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private static function getEntityProValue($entity, string $proName)
    {
        $tmpNodes     = \explode('_', $proName);
        $tmpNodes     = \array_map(function ($word) {
            return \ucfirst($word);
        }, $tmpNodes);
        $proName      = \implode('', $tmpNodes);
        $getterMethod = 'get' . $proName;

        if (!\method_exists($entity, $getterMethod)) {
            throw new \InvalidArgumentException('Entity object property getter method does not exist, properName=' . $proName);
        }

        return $entity->$getterMethod();
    }

    /**
     * @param object $entity
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private static function getClassMetaData($entity): array
    {
        // 不是对象
        if (!\is_object($entity) && !class_exists($entity)) {
            throw new \InvalidArgumentException('Entity is not an object');
        }

        // 对象实例不是实体
        $entities  = EntityCollector::getCollector();
        $className = \is_string($entity) ? $entity : \get_class($entity);
        if (!isset($entities[$className]['table']['name'])) {
            throw new \InvalidArgumentException('Object is not an entity object, className=' . $className);
        }

        return self::getTable($className);
    }

    /**
     * @param string $className
     *
     * @return array
     */
    private static function getTable(string $className): array
    {
        $entities   = EntityCollector::getCollector();
        $fields     = $entities[$className]['field'];
        $idProperty = $entities[$className]['table']['id'];
        $tableName  = $entities[$className]['table']['name'];
        $idColumn   = $fields[$idProperty]['column'];

        return [$tableName, $idProperty, $idColumn, $fields];
    }

    /**
     * @param string $className
     *
     * @return string
     */
    private static function getInstance(string $className): string
    {
        $collector = EntityCollector::getCollector();
        if (!isset($collector[$className]['instance'])) {
            return Pool::INSTANCE;
        }

        return $collector[$className]['instance'];
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private static function getFieldsFromOptions(array $options): array
    {
        return $options['fields']?? ['*'];
    }

    /**
     * @param QueryBuilder $query
     * @param array        $options
     *
     * @return QueryBuilder
     */
    private static function applyOptions(QueryBuilder $query, array $options)
    {
        if (isset($options['orderby'])) {
            $option = $options['orderby'];
            foreach ($option as $column => $order) {
                $query = $query->orderBy($column, $order);
            }
        }

        $limit  = $options['limit'] ?? null;
        $offset = $options['offset'] ?? 0;

        if ($limit !== null) {
            $query = $query->limit($limit, $offset);
        }

        return $query;
    }
}
