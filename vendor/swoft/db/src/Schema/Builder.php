<?php declare(strict_types=1);


namespace Swoft\Db\Schema;

use Closure;
use InvalidArgumentException;
use LogicException;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Connection\Connection;
use Swoft\Db\Connection\ConnectionManager;
use Swoft\Db\Database;
use Swoft\Db\DB;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Pool;
use Swoft\Db\Schema\Grammars\Grammar;
use Swoft\Db\Schema\Grammars\MySqlGrammar;
use Swoft\Stdlib\Helper\StringHelper;
use function array_map;
use function bean;
use function call_user_func;
use function get_class;
use function in_array;
use function is_callable;
use function sprintf;
use function strtolower;
use function tap;

/**
 * Class Schema Builder
 *
 * @since 2.0
 */
class Builder
{
    /**
     * Select db pool
     *
     * @var string
     */
    protected $poolName;

    /**
     * The database query grammar instance.
     *
     * @var Grammar
     */
    public $grammar;

    /**
     * The default string length for migrations.
     *
     * @var int
     */
    public static $defaultStringLength = 255;

    /**
     * @var array
     */
    public $grammars = [
        Database::MYSQL => MySqlGrammar::class
    ];

    /**
     * @var array
     */
    public static $builders = [
        Database::MYSQL => MySqlBuilder::class
    ];

    /**
     * The Blueprint resolver callback.
     *
     * @var Closure
     */
    protected $resolver;

    /**
     * @var string
     */
    protected $database;

    /**
     * @param string $database
     */
    public function setDatabase(string $database): void
    {
        $this->database = $database;
    }

    /**
     * Add custom builder
     *
     * @param string $driver
     * @param string $builderClass
     *
     * @return void
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function addBuilder(string $driver, string $builderClass): void
    {
        static::getBeanBuilder($builderClass);
        static::$builders[$driver] = $builderClass;
    }

    /**
     * @param string $builderClass
     *
     * @return Builder
     * @throws ContainerException
     * @throws ReflectionException
     */
    protected static function getBeanBuilder(string $builderClass): self
    {
        $builder = bean($builderClass);
        if (empty($builder)) {
            throw new InvalidArgumentException(sprintf('%s class is undefined @Bean()', $builderClass));
        }
        if (!$builder instanceof self) {
            throw new InvalidArgumentException(sprintf('%s class is not Builder instance', $builderClass));
        }
        return $builder;
    }

    /**
     * New builder instance
     *
     * @param mixed ...$params
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public static function new(...$params): Builder
    {
        /**
         * @var string|null  $poolName
         * @var Grammar|null $grammar
         */
        if (empty($params)) {
            $poolName = Pool::DEFAULT_POOL;
            $grammar  = null;
        } else {
            $poolName = $params[0];
            $grammar  = $params[1] ?? null;
        }
        // The driver builder
        $static = self::getBuilder($poolName);
        // Set schema config
        $static->setSchemaGrammar($grammar);

        return $static;
    }

    /**
     * @param string $poolName
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected static function getBuilder(string $poolName): Builder
    {
        /* @var Pool $pool */
        $pool = BeanFactory::getBean($poolName);

        $driver      = $pool->getDatabase()->getDriver();
        $builderName = static::$builders[$driver] ?? '';
        if (empty($builderName)) {
            throw new DbException(
                sprintf('Schema Builder(driver=%s) is not exist!', $driver)
            );
        }
        $builder           = static::getBeanBuilder($builderName);
        $builder->poolName = $poolName;
        return $builder;
    }

    /**
     * @param Grammar|null $grammar
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function setSchemaGrammar(Grammar $grammar = null): void
    {
        /* @var Pool $pool */
        $pool = BeanFactory::getBean($this->poolName);

        $driver = $pool->getDatabase()->getDriver();
        $prefix = $pool->getDatabase()->getPrefix();
        if (!empty($grammar)) {
            $grammar->setTablePrefix($prefix);
            $this->grammar = $grammar;
            return;
        }

        $grammarName = $this->grammars[$driver] ?? '';
        if (empty($grammarName)) {
            throw new DbException(
                sprintf('Grammar(driver=%s) is not exist!', $driver)
            );
        }

        $grammar = bean($grammarName);
        if (!$grammar instanceof Grammar) {
            throw new InvalidArgumentException('%s class is not Grammar instance', get_class($grammar));
        }

        $grammar->setTablePrefix($prefix);

        $this->grammar = $grammar;
    }

    /**
     * Determine if the given table exists.
     *
     * @param string $table
     *
     * @return bool
     * @throws DbException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function hasTable(string $table): bool
    {
        $table = $this->getTablePrefixName($table);

        return $this->getConnection()->statement($this->grammar->compileTableExists(), [$table]);
    }

    /**
     * Return complete table name
     *
     * @param string $table
     *
     * @return string
     */
    public function getTablePrefixName(string $table): string
    {
        $prefix = $this->grammar->getTablePrefix();
        return $prefix . $table;
    }

    /**
     * @param string $table
     *
     * @return mixed|string
     */
    protected function removeTablePrefix(string $table)
    {
        $prefix = $this->grammar->getTablePrefix();
        $table  = $prefix ? StringHelper::replaceFirst($prefix, '', $table) : $table;
        return $table;
    }

    /**
     * Determine if the given table has a given column.
     *
     * @param string $table
     * @param string $column
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function hasColumn(string $table, string $column): bool
    {
        return in_array(
            strtolower($column), array_map('strtolower', $this->getColumnListing($table))
        );
    }

    /**
     * Determine if the given table has given columns.
     *
     * @param       $table
     * @param array $columns
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function hasColumns($table, array $columns)
    {
        $tableColumns = array_map('strtolower', $this->getColumnListing($table));

        foreach ($columns as $column) {
            if (!in_array(strtolower($column), $tableColumns)) {
                return false;
            }
        }

        return true;
    }

    /**
     *  Get the column listing for a given table.
     *
     * @param string $table
     *
     * @return array
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function getColumnListing(string $table)
    {
        $table      = $this->getTablePrefixName($table);
        $connection = $this->getConnection();
        $results    = $connection->select(
            $this->grammar->compileColumnListing(), [$table], false
        );
        return $connection->getPostProcessor()->processColumnListing($results);
    }

    /**
     * Get Columns detail
     *
     * @param string $table
     * @param array  $addSelect
     *
     * @return array
     */
    public function getColumnsDetail(string $table, array $addSelect = []): array
    {
        return [];
    }

    /**
     * Get table schema
     *
     * @param string $table
     * @param array  $addSelect
     * @param string $exclude
     * @param string $tablePrefix
     *
     * @return array
     */
    public function getTableSchema(
        string $table,
        array $addSelect = [],
        string $exclude = '',
        string $tablePrefix = ''
    ): array {
        return [];
    }

    /**
     * Check database exists
     *
     * @return bool
     */
    public function checkDatabaseExists(): bool
    {
        return true;
    }

    /**
     * Modify a table on the schema.
     *
     * @param string  $table
     * @param Closure $callback
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function table(string $table, Closure $callback)
    {
        $this->build($this->createBlueprint($table, $callback));
    }

    /**
     * Create a new table on the schema.
     *
     * @param string  $table
     * @param Closure $callback
     * @param bool    $ifNotExists
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function create(string $table, Closure $callback, bool $ifNotExists = false)
    {
        $this->build(tap($this->createBlueprint($table), function (Blueprint $blueprint) use ($callback, $ifNotExists) {
            $blueprint->create($ifNotExists);

            $callback($blueprint);
        }));
    }

    /**
     * Create a if not exist, new table on the schema.
     *
     * @param string  $table
     * @param Closure $callback
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function createIfNotExists(string $table, Closure $callback)
    {
        $this->build(tap($this->createBlueprint($table), function (Blueprint $blueprint) use ($callback) {
            $blueprint->create(true);

            $callback($blueprint);
        }));
    }

    /**
     * Drop a table from the schema.
     *
     * @param string $table
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function drop(string $table)
    {
        $this->build(tap($this->createBlueprint($table), function (Blueprint $blueprint) {
            $blueprint->drop();
        }));
    }

    /**
     * Drop a table from the schema if it exists.
     *
     * @param string $table
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function dropIfExists(string $table)
    {
        $this->build(tap($this->createBlueprint($table), function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        }));
    }

    /**
     *  Rename a table on the schema.
     *
     * @param string $from
     * @param string $to
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function rename(string $from, string $to)
    {
        $this->build(tap($this->createBlueprint($from), function (Blueprint $blueprint) use ($to) {
            $blueprint->rename($to);
        }));
    }

    /**
     * Enable foreign key constraints.
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function enableForeignKeyConstraints()
    {
        return $this->getConnection()->statement(
            $this->grammar->compileEnableForeignKeyConstraints()
        );
    }

    /**
     * Disable foreign key constraints.
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function disableForeignKeyConstraints()
    {
        return $this->getConnection()->statement(
            $this->grammar->compileDisableForeignKeyConstraints()
        );
    }

    /**
     * Execute the blueprint to build / modify the table.
     *
     * @param Blueprint $blueprint
     *
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function build(Blueprint $blueprint)
    {
        $blueprint->build($this);
    }

    /**
     * Create a new command set with a Closure.
     *
     * @param string       $table
     * @param Closure|null $callback
     *
     * @return Blueprint
     * @throws ContainerException
     * @throws ReflectionException
     */
    protected function createBlueprint(string $table, Closure $callback = null)
    {
        $prefix = $this->grammar->getTablePrefix();

        if (is_callable($this->resolver)) {
            return call_user_func($this->resolver, $table, $callback, $prefix);
        }

        return Blueprint::new($table, $callback, $prefix);
    }

    /**
     * Set the Schema Blueprint resolver callback.
     *
     * @param \Closure $resolver
     *
     * @return void
     */
    public function blueprintResolver(Closure $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get the database connection instance.
     *
     * @return Connection
     * @throws DbException
     */
    public function getConnection()
    {
        $connection = DB::connection($this->poolName);

        if (isset($this->database)) {
            $connectionDatabase = $connection->getSelectDb() ?: $connection->getDb();
            if ($this->database !== $connectionDatabase) {
                $connection->db($this->database);
            }
        }

        return $connection;
    }

    /**
     * Drop all tables from the database.
     *
     * @return void
     *
     * @throws LogicException
     */
    public function dropAllTables()
    {
        throw new LogicException('This database driver does not support dropping all tables.');
    }

    /**
     * Drop all views from the database.
     *
     * @return void
     *
     * @throws LogicException
     */
    public function dropAllViews()
    {
        throw new LogicException('This database driver does not support dropping all views.');
    }

    /**
     * Drop all types from the database.
     *
     * @return void
     *
     * @throws LogicException
     */
    public function dropAllTypes()
    {
        throw new LogicException('This database driver does not support dropping all types.');
    }

    /**
     * Get connection database name
     *
     * @return string
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function getDatabaseName(): string
    {
        if (isset($this->database)) {
            return $this->database;
        }

        $connection = $this->getConnection();
        $db         = $connection->getSelectDb() ?: $connection->getDb();
        // release
        $connection->release();

        return $db;
    }

    /**
     * convert database to php type
     *
     * @param string $databaseType
     *
     * @return string
     */
    public function convertType(string $databaseType): string
    {
        return $this->grammar->convertType($databaseType);
    }
}
