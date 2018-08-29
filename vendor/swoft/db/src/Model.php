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

use Swoft\Contract\Arrayable;
use Swoft\Core\ResultInterface;
use Swoft\Db\Bean\Collector\EntityCollector;
use Swoft\Helper\StringHelper;

/**
 * ActiveRecord
 */
class Model implements \ArrayAccess, \Iterator, Arrayable,\JsonSerializable
{
    /**
     * Old data
     *
     * @var array
     */
    private $attrs = [];

    /**
     * Model constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Insert data to db
     *
     * @return ResultInterface
     */
    public function save(): ResultInterface
    {
        return Executor::save($this);
    }

    /**
     * Delete data from db
     *
     * @return ResultInterface
     */
    public function delete(): ResultInterface
    {
        return Executor::delete($this);
    }

    /**
     * @param array $condition
     *
     * @return ResultInterface
     */
    public static function deleteOne(array $condition): ResultInterface
    {
        return Executor::deleteOne(static::class, $condition);
    }

    /**
     * @param array $condition
     *
     * @return ResultInterface
     */
    public static function deleteAll(array $condition): ResultInterface
    {
        return Executor::deleteAll(static::class, $condition);
    }

    /**
     * @param array $rows
     *
     * @return ResultInterface
     */
    public static function batchInsert(array $rows): ResultInterface
    {
        return Executor::batchInsert(static::class, $rows);
    }

    /**
     * Delete data by id
     *
     * @param mixed $id ID
     *
     * @return ResultInterface
     */
    public static function deleteById($id): ResultInterface
    {
        return Executor::deleteById(static::class, $id);
    }

    /**
     * Delete by ids
     *
     * @param array $ids
     *
     * @return ResultInterface
     */
    public static function deleteByIds(array $ids): ResultInterface
    {
        return Executor::deleteByIds(static::class, $ids);
    }

    /**
     * @param array $attributes
     * @param array $condition
     *
     * @return ResultInterface
     */
    public static function updateOne(array $attributes, array $condition): ResultInterface
    {
        return Executor::updateOne(static::class, $attributes, $condition);
    }

    /**
     * @param array $attributes
     * @param array $condition
     *
     * @return ResultInterface
     */
    public static function updateAll(array $attributes, array $condition): ResultInterface
    {
        return Executor::updateAll(static::class, $attributes, $condition);
    }

    /**
     * Update data
     *
     * @return ResultInterface
     */
    public function update(): ResultInterface
    {
        return Executor::update($this);
    }

    /**
     * Find data from db
     *
     * @return ResultInterface
     */
    public function find(): ResultInterface
    {
        return Executor::find($this);
    }

    /**
     * Determine if Entity exist ?
     *
     * @param mixed $id
     *
     * @return ResultInterface
     */
    public static function exist($id): ResultInterface
    {
        return Executor::exist(static::class, $id);
    }

    /**
     * @param string $column
     * @param array  $condition
     *
     * @return ResultInterface
     */
    public static function count(string $column = '*', array $condition = []): ResultInterface
    {
        return Executor::count(static::class, $column, $condition);
    }

    /**
     * @param array $counters
     * @param array $condition
     *
     * @return ResultInterface
     */
    public static function counter(array $counters, array $condition = []):ResultInterface
    {
        return Executor::counter(static::class, $counters, $condition);
    }

    /**
     * @param array $condition
     * @param array $options
     *
     * @return ResultInterface
     */
    public static function findOne(array $condition, array $options = []): ResultInterface
    {
        return Executor::findOne(static::class, $condition, $options);
    }

    /**
     * @param array $condition
     * @param array $options
     *
     * @return ResultInterface
     */
    public static function findAll(array $condition = [], array $options = []): ResultInterface
    {
        return Executor::findAll(static::class, $condition, $options);
    }

    /**
     * Find by id
     *
     * @param mixed $id
     * @param array $options
     *
     * @return ResultInterface
     */
    public static function findById($id, array $options = []): ResultInterface
    {
        return Executor::findById(static::class, $id, $options);
    }

    /**
     * Find by ids
     *
     * @param array $ids
     * @param array $options
     *
     * @return ResultInterface
     */
    public static function findByIds(array $ids, array $options = []): ResultInterface
    {
        return Executor::findByIds(static::class, $ids, $options);
    }

    /**
     * Get the QueryBuilder
     *
     * @return QueryBuilder
     */
    public static function query(): QueryBuilder
    {
        return Executor::query(static::class);
    }

    /**
     * @return array
     */
    public function getAttrs(): array
    {
        return $this->attrs;
    }

    /**
     * @param array $attrs
     */
    public function setAttrs(array $attrs)
    {
        $this->attrs = $attrs;
    }

    /**
     * @param array $attributes
     *
     * $attributes = [
     *     'name' => $value
     * ]
     *
     * @return \Swoft\Db\Model
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $name => $value) {
            $methodName = sprintf('set%s', ucfirst($name));
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $entities = EntityCollector::getCollector();
        $columns  = $entities[static::class]['field'];
        $data = [];
        foreach ($columns as $propertyName => $column) {
            if (!isset($column['column'])) {
                continue;
            }
            $methodName = StringHelper::camel('get' . $propertyName);
            if (!\method_exists($this, $methodName)) {
                continue;
            }

            $value = $this->$methodName();
            if($value === null){
                continue;
            }
            $data[$propertyName] = $value;
        }

        return $data;
    }

    /**
     * @param int $options
     *
     * @return string
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return \json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        $data = $this->toArray();

        return isset($data[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $data  = $this->toArray();

        return $data[$offset]??null;
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->fill([$offset => $value]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->attrs);
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->attrs);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->attrs);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid(): bool
    {
        return $this->current() !== false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->attrs);
    }

    function jsonSerialize()
    {
        return $this->toArray();
    }
}
