<?php declare(strict_types=1);


namespace Swoft\Db\Eloquent;

use ArrayAccess;
use Closure;
use DateTimeInterface;
use Generator;
use JsonSerializable;
use ReflectionException;
use Swoft\Aop\Proxy;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Concern\HasAttributes;
use Swoft\Db\Concern\HasEvent;
use Swoft\Db\Concern\HasTimestamps;
use Swoft\Db\Concern\HidesAttributes;
use Swoft\Db\Connection\Connection;
use Swoft\Db\DB;
use Swoft\Db\DbEvent;
use Swoft\Db\EntityRegister;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Query\Builder as QueryBuilder;
use Swoft\Stdlib\Contract\Arrayable;
use Swoft\Stdlib\Contract\Jsonable;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\Stdlib\Helper\PhpHelper;
use Swoft\Stdlib\Helper\Str;
use Throwable;
use function bean;

/**
 * Class Model
 *
 * @since 2.0
 * @method static static make(array $attributes = [])
 * @method static Builder whereKey($id)
 * @method static Builder whereKeyNot($id)
 * @method static Builder where($column, $operator = null, $value = null, string $boolean = 'and')
 * @method static Builder orWhere($column, $operator = null, $value = null)
 * @method static Builder latest(string $column = null)
 * @method static Builder oldest(string $column = null)
 * @method static Collection hydrate(array $items)
 * @method static Collection fromQuery(string $query, array $bindings = [])
 * @method static static find($id, array $columns = ['*'])
 * @method static Collection findMany(array $ids, array $columns = ['*'])
 * @method static Builder findOrFail($id, array $columns = ['*'])
 * @method static Builder findOrNew($id, array $columns = ['*'])
 * @method static Builder firstOrNew(array $attributes, array $values = [])
 * @method static Builder firstOrCreate(array $attributes, array $values = [])
 * @method static static updateOrCreate(array $attributes, array $values = [])
 * @method static bool updateOrInsert(array $attributes, array $values = [])
 * @method static int batchUpdateByIds(array $values)
 * @method static int updateAllCounters(array $attributes, array $counters, array $extra = [])
 * @method static int updateAllCountersAdoptPrimary(array $attributes, array $counters, array $extra = [])
 * @method static int updateAllCountersById(array $ids, array $counters, array $extra = [])
 * @method static bool modifyById(int $id, array $values)
 * @method static bool modify(array $attributes, array $values)
 * @method static Builder firstOrFail(array $columns = ['*'])
 * @method static Builder firstOr(array $columns = ['*'], Closure $callback = null)
 * @method static mixed value(string $column)
 * @method static static[] get(array $columns = ['*'])
 * @method static static[] getModels($columns = ['*'])
 * @method static Generator cursor()
 * @method static bool chunkById(int $count, callable $callback, string $column = null, string $alias = null)
 * @method static void  enforceOrderBy()
 * @method static Collection pluck(string $column, string $key = null)
 * @method static static  create(array $attributes = [])
 * @method static Builder select(string ...$columns)
 * @method static Builder selectSub(Closure|QueryBuilder|string $query, string $as)
 * @method static Builder selectRaw(string $expression, array $bindings = [])
 * @method static Builder fromSub(Closure|QueryBuilder|string $query, string $as)
 * @method static Builder fromRaw(string $expression, array $bindings = [])
 * @method static Builder createSub(Closure|QueryBuilder|string $query)
 * @method static Builder parseSub(Closure|QueryBuilder|string $query)
 * @method static Builder addSelect(array $column)
 * @method static Builder distinct()
 * @method static Builder from(string $table)
 * @method static Builder db(string $dbname)
 * @method static Builder join(string $table, Closure|string $first, string $operator = null, string $second = null, string $type = 'inner', bool $where = false)
 * @method static Builder joinWhere(string $table, Closure|string $first, string $operator, string $second, string $type = 'inner')
 * @method static Builder joinSub(Closure|QueryBuilder|string $query, string $as, Closure|string $first, string $operator = null, string $second = null, string $type = 'inner', bool $where = false)
 * @method static Builder leftJoin(string $table, Closure|string $first, string $operator = null, string $second = null)
 * @method static Builder leftJoinWhere(string $table, string $first, string $operator, string $second)
 * @method static Builder leftJoinSub(Closure|QueryBuilder|string $query, string $as, string $first, string $operator = null, string $second = null)
 * @method static Builder rightJoin(string $table, Closure|string $first, string $operator = null, string $second = null)
 * @method static Builder rightJoinWhere(string $table, string $first, string $operator, string $second)
 * @method static Builder rightJoinSub(Closure|QueryBuilder|string $query, string $as, string $first, string $operator = null, string $second = null)
 * @method static Builder crossJoin(string $table, Closure|string $first = null, string $operator = null, string $second = null)
 * @method static void mergeWheres(array $wheres, array $bindings)
 * @method static Builder whereColumn(string|array $first, string $operator = null, string $second = null, string $boolean = 'and')
 * @method static Builder orWhereColumn(string|array $first, string $operator = null, string $second = null)
 * @method static Builder whereRaw(string $sql, array $bindings = [], string $boolean = 'and')
 * @method static Builder orWhereRaw(string $sql, array $bindings = [])
 * @method static Builder whereIn(string $column, mixed $values, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereIn(string $column, mixed $values)
 * @method static Builder whereNotIn(string $column, mixed $values, string $boolean = 'and')
 * @method static Builder orWhereNotIn(string $column, mixed $values)
 * @method static Builder whereIntegerInRaw(string $column, Arrayable|array $values, string $boolean = 'and', bool $not = false)
 * @method static Builder whereIntegerNotInRaw(string $column, Arrayable|array $values, string $boolean = 'and')
 * @method static Builder whereNull(string $column, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereNull(string $column)
 * @method static Builder whereNotNull(string $column, string $boolean = 'and')
 * @method static Builder whereBetween(string $column, array $values, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereBetween(string $column, array $values)
 * @method static Builder whereNotBetween(string $column, array $values, string $boolean = 'and')
 * @method static Builder orWhereNotBetween(string $column, array $values)
 * @method static Builder orWhereNotNull(string $column)
 * @method static Builder whereDate(string $column, $operator, DateTimeInterface|string $value = null, string $boolean = 'and')
 * @method static Builder orWhereDate(string $column, string $operator, DateTimeInterface|string $value = null)
 * @method static Builder whereTime(string $column, $operator, DateTimeInterface|string $value = null, string $boolean = 'and')
 * @method static Builder orWhereTime(string $column, string $operator, DateTimeInterface|string $value = null)
 * @method static Builder whereDay(string $column, string $operator, DateTimeInterface|string $value = null, string $boolean = 'and')
 * @method static Builder orWhereDay(string $column, string $operator, DateTimeInterface|string $value = null)
 * @method static Builder whereMonth(string $column, string $operator, DateTimeInterface|string $value = null, string $boolean = 'and')
 * @method static Builder orWhereMonth(string $column, string $operator, DateTimeInterface|string $value = null)
 * @method static Builder whereYear(string $column, string $operator, DateTimeInterface|string|int $value = null, string $boolean = 'and')
 * @method static Builder orWhereYear(string $column, string $operator, DateTimeInterface|string|int $value = null)
 * @method static Builder whereNested(Closure $callback, string $boolean = 'and')
 * @method static Builder forNestedWhere()
 * @method static Builder addNestedWhereQuery(QueryBuilder $query, string $boolean = 'and')
 * @method static Builder whereSub(string $column, string $operator, Closure $callback, string $boolean)
 * @method static Builder whereExists(Closure $callback, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereExists(Closure $callback, bool $not = false)
 * @method static Builder whereNotExists(Closure $callback, string $boolean = 'and')
 * @method static Builder orWhereNotExists(Closure $callback)
 * @method static Builder addWhereExistsQuery(Builder $query, string $boolean = 'and', bool $not = false)
 * @method static Builder whereRowValues(array $columns, string $operator, array $values, string $boolean = 'and')
 * @method static Builder orWhereRowValues(array $columns, string $operator, array $values)
 * @method static Builder whereJsonContains(string $column, $value, string $boolean = 'and', bool $not = false)
 * @method static Builder orWhereJsonContains(string $column, mixed $value)
 * @method static Builder whereJsonDoesntContain(string $column, mixed $value, string $boolean = 'and')
 * @method static Builder orWhereJsonDoesntContain(string $column, mixed $value)
 * @method static Builder whereJsonLength(string $column, mixed $operator, mixed $value = null, string $boolean = 'and')
 * @method static Builder orWhereJsonLength(string $column, mixed $operator, mixed $value = null)
 * @method static Builder groupBy(...$groups)
 * @method static Builder having(string $column, string $operator = null, string $value = null, string $boolean = 'and')
 * @method static Builder orHaving(string $column, string $operator = null, string $value = null)
 * @method static Builder havingBetween(string $column, array $values, string $boolean = 'and', bool $not = false)
 * @method static Builder havingRaw(string $sql, array $bindings = [], string $boolean = 'and')
 * @method static Builder orHavingRaw(string $sql, array $bindings = [])
 * @method static Builder orderBy(string $column, string $direction = 'asc')
 * @method static Builder orderByDesc(string $column)
 * @method static Builder inRandomOrder(string $seed = '')
 * @method static Builder orderByRaw(string $sql, array $bindings = [])
 * @method static Builder skip(int $value)
 * @method static string  implode(string $column, string $glue = '')
 * @method static Builder offset(int $value)
 * @method static Builder take(int $value)
 * @method static string  insertGetId(array $values, string $sequence = null)
 * @method static bool    insert(array $values)
 * @method static Builder limit(int $value)
 * @method static Builder forPage(int $page, int $perPage = 15)
 * @method static Builder forPageAfterId(int $perPage = 15, int $lastId = null, string $column = null)
 * @method static Builder forPageBeforeId(int $perPage = 15, int $lastId = null, string $column = null)
 * @method static array   paginate(int $page = 1, int $perPage = 15, array $columns = ['*'])
 * @method static array   paginateById(int $perPage = 15, int $lastId = null, array $columns = ['*'], bool $useAfter = false, string $primary = null)
 * @method static array   getBindings()
 * @method static string  toSql()
 * @method static bool    exists()
 * @method static bool    doesntExist()
 * @method static int     count(string $columns = '*')
 * @method static float|int min(string $column)
 * @method static float|int max(string $column)
 * @method static float|int sum(string $column)
 * @method static float|int avg($column)
 * @method static float|int average(string $column)
 * @method static void truncate()
 * @method static Builder useWritePdo()
 * @method static int getCountForPagination(array $columns = ['*'])
 */
abstract class Model implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    use HidesAttributes, HasAttributes, HasTimestamps, HasEvent;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    protected const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    protected const UPDATED_AT = 'updated_at';

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $swoftExists = false;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     *
     * @throws DbException
     */
    public function __construct(array $attributes = [])
    {
        $this->syncOriginal();

        $this->fill($attributes);
    }


    /**
     * Create a new model
     *
     * @param array $attributes
     *
     * @return static
     * @throws DbException
     */
    public static function new(array $attributes = []): self
    {
        try {
            /* @var static $self */
            $self = bean(Proxy::getClassName(static::class));
        } catch (Throwable $e) {
            throw new DbException($e->getMessage());
        }

        $self->syncOriginal();
        $self->fill($attributes);
        $self->swoftExists = false;

        return $self;
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     *
     * @return Model
     * @throws DbException
     */
    public function fill(array $attributes): self
    {
        $this->setRawAttributes($attributes);
        return $this;
    }

    /**
     * Qualify the given column name by the model's table.
     *
     * @param string $column
     *
     * @return string
     * @throws DbException
     */
    public function qualifyColumn(string $column): string
    {
        if (Str::contains($column, '.')) {
            return $column;
        }

        return $this->getTable() . '.' . $column;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @param bool  $exists
     *
     * @return static
     * @throws DbException
     */
    public function newInstance(array $attributes = [], bool $exists = false): self
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = static::new($attributes);

        $model->swoftExists = $exists;

        return $model;
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param array $attributes
     *
     * @return static
     * @throws
     */
    public function newFromBuilder($attributes = [])
    {
        $model = $this->newInstance([], true);

        $model->setRawAttributes((array)$attributes, true);
        return $model;
    }

    /**
     * Get all of the models from the database.
     *
     * @param array $columns
     *
     * @return Collection
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public static function all(array $columns = ['*']): Collection
    {
        return static::new()->newQuery()->get($columns);
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param string    $column
     * @param float|int $amount
     * @param array     $extra
     *
     * @return mixed
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function increment(string $column, $amount = 1, array $extra = [])
    {
        return $this->incrementOrDecrement($column, $amount, $extra, 'increment');
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param string    $column
     * @param float|int $amount
     * @param array     $extra
     *
     * @return mixed
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function decrement(string $column, $amount = 1, array $extra = [])
    {
        return $this->incrementOrDecrement($column, $amount, $extra, 'decrement');
    }

    /**
     * Run the increment or decrement method on the model.
     *
     * @param string    $column
     * @param float|int $amount
     * @param array     $extra
     * @param string    $method
     *
     * @return mixed
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function incrementOrDecrement(string $column, $amount, array $extra, string $method)
    {
        $query = $this->newModelQuery();

        if (!$this->swoftExists) {
            return $query->{$method}($column, $amount, $extra);
        }

        $this->incrementOrDecrementAttributeValue($column, $amount, $extra, $method);

        return $query->where(
            $this->getKeyName(), $this->getKey()
        )->{$method}($column, $amount, $extra);
    }

    /**
     * Increment the underlying attribute value and sync with original.
     *
     * @param string    $column
     * @param float|int $amount
     * @param array     $extra
     * @param string    $method
     *
     * @return void
     * @throws DbException
     */
    protected function incrementOrDecrementAttributeValue(string $column, $amount, $extra, $method)
    {
        $columnValue = $method === 'increment' ? $amount : $amount * -1;
        $this->setModelAttribute($column, $this->getAttributeValue($column) + $columnValue);

        $this->fill($extra);

        $this->syncOriginalAttribute($column);
    }

    /**
     * Update the model in the database.
     *
     * @param array $attributes
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function update(array $attributes = [])
    {
        if (!$this->swoftExists) {
            return false;
        }

        return $this->fill($attributes)->save();
    }

    /**
     * Update counters by primary key
     *
     * @param array $counters
     * @param array $extra
     *
     * @return int
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function updateCounters(array $counters, array $extra = []): int
    {
        if (!$this->swoftExists) {
            return 0;
        }

        $query = $this->newModelQuery();
        $key   = $this->getKeyName();
        $id    = $this->getAttributeValue($key);

        return $query->updateAllCountersById((array)$id, $counters, $extra);
    }


    /**
     * Save the model to the database.
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function save()
    {
        $query = $this->newModelQuery();

        // If the "saving" event returns false we'll bail out of the save and return
        // false, indicating that the save failed. This provides a chance for any
        // listeners to cancel save operations if validations fail or whatever.
        if ($this->fireEvent(DbEvent::MODEL_SAVING) === false) {
            return false;
        }

        // If the model already exists in the database we can just update our record
        // that is already in this database using the current IDs in this "where"
        // clause to only update this model. Otherwise, we'll just insert them.
        if ($this->swoftExists) {
            $saved = $this->isDirty() ?
                $this->performUpdate($query) : true;
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->performInsert($query);
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->finishSave();
        }

        return $saved;
    }

    /**
     * Save the model to the database using transaction.
     *
     * @param array $options
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function saveOrFail(array $options = [])
    {
        return $this->getConnection()->transaction(function () use ($options) {
            return $this->save();
        });
    }

    /**
     * Perform any actions that are necessary after the model is saved.
     *
     * @return void
     * @throws ContainerException
     */
    protected function finishSave()
    {
        $this->fireEvent(DbEvent::MODEL_SAVED);

        $this->syncOriginal();
    }

    /**
     * Perform a model update operation.
     *
     * @param Builder $query
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function performUpdate(Builder $query)
    {
        // If the updating event returns false, we will cancel the update operation so
        // developers can hook Validation systems into their models and cancel this
        // operation if the model does not pass validation. Otherwise, we update.
        if ($this->fireEvent(DbEvent::MODEL_UPDATING) === false) {
            return false;
        }

        // Once we have run the update operation, we will fire the "updated" event for
        // this model instance. This will allow developers to hook into these after
        // models are updated, giving them a chance to do any special processing.
        $dirty = $this->getDirty();

        if (count($dirty) > 0) {
            $this->setKeysForSaveQuery($query)->update($dirty);

            $this->syncChanges();

            $this->fireEvent(DbEvent::MODEL_UPDATED);
        }

        return true;
    }

    /**
     * Set the keys for a save update query.
     *
     * @param Builder $query
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery());

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @return mixed
     * @throws DbException
     */
    protected function getKeyForSaveQuery()
    {
        return $this->modelOriginal[$this->getKeyName()]
            ?? $this->getKey();
    }

    /**
     * Perform a model insert operation.
     *
     * @param Builder $query
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function performInsert(Builder $query)
    {
        if ($this->fireEvent(DbEvent::MODEL_CREATING) === false) {
            return false;
        }

        // If the model has an incrementing key, we can use the "insertGetId" method on
        // the query builder, which will give us back the final inserted ID for this
        // table from the database. Not all tables have to be incrementing though.
        $attributes = $this->getArrayableAttributes();

        if ($this->getIncrementing()) {
            $this->insertAndSetId($query, $attributes);
        }

        // If the table isn't incrementing we'll simply insert these attributes as they
        // are. These attribute arrays must contain an "id" column previously placed
        // there by the developer as the manually determined key for these models.
        else {
            if (empty($attributes)) {
                return true;
            }

            $query->insert($attributes);
        }

        // We will go ahead and set the exists property to true, so that it is set when
        // the created event is fired, just in case the developer tries to update it
        // during the event. This will allow them to do so and run an update here.
        $this->swoftExists = true;

        $this->fireEvent(DbEvent::MODEL_CREATED);

        return true;
    }

    /**
     * Insert the given attributes and set the ID on the model.
     *
     * @param Builder $query
     * @param array   $attributes
     *
     * @return void
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function insertAndSetId(Builder $query, $attributes)
    {
        $keyName = $this->getKeyName();
        $id      = $query->insertGetId($attributes, $keyName);

        $this->setModelAttribute($keyName, $id);
    }

    /**
     * Delete the model from the database.
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function delete(): bool
    {
        if (is_null($this->getKeyName())) {
            throw new DbException('No primary key defined on model.');
        }

        // If the model doesn't exist, there is nothing to delete so we'll just return
        // immediately and not do anything else. Otherwise, we will continue with a
        // deletion process on the model, firing the proper events, and so forth.
        if (!$this->swoftExists) {
            return false;
        }

        if ($this->fireEvent(DbEvent::MODEL_DELETING) === false) {
            return false;
        }

        $this->performDeleteOnModel();

        // Once the model has been deleted, we will fire off the deleted event so that
        // the developers may hook into post-delete operations. We will then return
        // a boolean true as the delete is presumably successful on the database.
        $this->fireEvent(DbEvent::MODEL_DELETED);

        return true;
    }

    /**
     * Force a hard delete on a soft deleted model.
     *
     * This method protects developers from running forceDelete when trait is missing.
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function forceDelete()
    {
        return $this->delete();
    }

    /**
     * Perform the actual delete query on this model instance.
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function performDeleteOnModel()
    {
        $this->setKeysForSaveQuery($this->newModelQuery())->delete();

        $this->swoftExists = false;
    }

    /**
     * Begin querying the model.
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public static function query()
    {
        return static::new()->newQuery();
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function newQuery()
    {
        return $this->newModelQuery();
    }

    /**
     * Get a new query builder that doesn't have any global scopes or eager loading.
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function newModelQuery()
    {
        return $this->newEloquentBuilder($this->newBaseQueryBuilder())->setModel($this);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param QueryBuilder $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return QueryBuilder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function newBaseQueryBuilder()
    {
        $poolName = EntityRegister::getPool($this->getClassName());
        return QueryBuilder::new($poolName, null, null);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array $models
     *
     * @return Collection
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function newCollection(array $models = [])
    {
        return Collection::new($models);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     * @throws DbException
     */
    public function toArray(): array
    {
        return $this->attributesToArray();
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param int $options
     *
     * @return string
     * @throws DbException
     */
    public function toJson(int $options = 0): string
    {
        return JsonHelper::encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     * @throws DbException
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Reload a fresh model instance from the database.
     *
     * @return null|$this|object|Builder|Model
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function fresh()
    {
        if (!$this->swoftExists) {
            return $this;
        }

        return $this->newModelQuery()
            ->where($this->getKeyName(), $this->getKey())
            ->first();
    }

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return $this
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function refresh()
    {
        if (!$this->swoftExists) {
            return $this;
        }

        $this->setRawAttributes(
            $this->newModelQuery()->findOrFail($this->getKey())->modelAttributes
        );

        $this->syncOriginal();

        return $this;
    }

    /**
     * Determine if two models have the same ID and belong to the same table.
     *
     * @param Model $model
     *
     * @return bool
     * @throws DbException
     */
    public function is(Model $model): bool
    {
        return !is_null($model)
            && $this->getKey() === $model->getKey()
            && $this->getTable() === $model->getTable();
    }

    /**
     * Determine if two models are not the same.
     *
     * @param Model $model
     *
     * @return bool
     * @throws DbException
     */
    public function isNot(Model $model): bool
    {
        return !$this->is($model);
    }

    /**
     * Get the database connection for the model.
     *
     * @throws DbException
     * @throws DbException
     */
    public function getConnection(): Connection
    {
        $pool = EntityRegister::getPool($this->getClassName());

        return DB::connection($pool);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     * @throws DbException
     */
    public function getTable()
    {
        return EntityRegister::getTable($this->getClassName());
    }

    /**
     * Get the primary key for the model.
     *
     * @return string
     * @throws DbException
     */
    public function getKeyName()
    {
        return EntityRegister::getId($this->getClassName());
    }

    /**
     * Get the table qualified key name.
     *
     * @return string
     * @throws DbException
     */
    public function getQualifiedKeyName()
    {
        return $this->qualifyColumn($this->getKeyName());
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return EntityRegister::getIdIncrementing($this->getClassName());
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return string
     * @throws DbException
     */
    public function getKey()
    {
        return $this->getAttributeValue($this->getKeyName());
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     * @throws DbException
     */
    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param mixed $offset
     *
     * @return bool
     * @throws DbException
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getModelAttribute($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     * @throws DbException
     */
    public function offsetGet($offset)
    {
        return $this->getAttributeValue($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     * @throws DbException
     */
    public function offsetSet($offset, $value)
    {
        $this->setModelAttribute($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->modelAttributes[$offset]);
    }

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param string $key
     *
     * @return bool
     * @throws DbException
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Unset an attribute on the model.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return Proxy::getClassName(static::class);
    }

    /**
     * Get entity table name
     *
     * @return string
     * @throws DbException
     */
    public static function tableName()
    {
        $className = Proxy::getClassName(static::class);

        return EntityRegister::getTable($className);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, ['increment', 'decrement'])) {
            return $this->$method(...$parameters);
        }

        return PhpHelper::call([$this->newModelQuery(), $method], ...$parameters);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     * @throws DbException
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static())->$method(...$parameters);
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     * @throws DbException
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
