<?php declare(strict_types=1);


namespace Swoft\Db\Schema;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Query\Builder as QueryBuilder;
use Swoft\Stdlib\Helper\StringHelper;

/**
 * Class Builder
 *
 * @Bean(scope=Bean::PROTOTYPE)
 *
 * @since 2.0
 */
class MySqlBuilder extends Builder
{

    /**
     * Determine if the given table exists.
     *
     * @param string $table
     *
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     * @throws DbException
     */
    public function hasTable(string $table): bool
    {
        $table      = $this->getTablePrefixName($table);
        $connection = $this->getConnection();

        return count($connection->select(
                $this->grammar->compileTableExists(), [$this->getDatabaseName(), $table], false
            )) > 0;
    }

    /**
     * Get the column listing for a given table.
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
            $this->grammar->compileColumnListing(), [$this->getDatabaseName(), $table], false
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
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function getColumnsDetail(string $table, array $addSelect = []): array
    {
        $columns = [
            'COLUMN_NAME as name',
            'DATA_TYPE as type',
            'COLUMN_DEFAULT as default',
            'COLUMN_KEY as key',
            'IS_NULLABLE as nullable',
            'COLUMN_TYPE as columnType',
            'COLUMN_COMMENT as columnComment',
            'CHARACTER_MAXIMUM_LENGTH as length',
            'EXTRA as extra'
        ];
        $query   = QueryBuilder::new($this->poolName, null, null);
        $results = $query->fromRaw('information_schema.columns')
            ->where('table_schema', $this->getDatabaseName())
            ->where('table_name', $this->getTablePrefixName($table))
            ->useWritePdo()
            ->select(...$columns)
            ->addSelect($addSelect)
            ->get()
            ->toArray();
        foreach ($results as &$item) {
            $item = (array)$item;
        }
        unset($item);
        return $results;
    }

    /**
     * Get table schema, support batch get
     *
     * @param string $table
     * @param array  $addSelect
     * @param string $exclude
     * @param string $likeTable
     *
     * @return array
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function getTableSchema(
        string $table,
        array $addSelect = [],
        string $exclude = '',
        string $likeTable = ''
    ): array {
        $query   = QueryBuilder::new($this->poolName, null, null);
        $columns = [
            'TABLE_NAME as name',
            'TABLE_COMMENT as comment',
        ];
        $results = $query->fromRaw('information_schema.tables')
            ->where('table_schema', $this->getDatabaseName())
            ->when($table, function (QueryBuilder $query, $tableName) {
                $query->whereIn('table_name', array_map(
                        [$this, 'getTablePrefixName'],
                        explode(',', $tableName)
                    )
                );
            }, function (QueryBuilder $query, $tableName) use ($likeTable, $exclude) {
                $paramsEmpty = empty($exclude) && empty($tableName) && empty($likeTable);
                if ($paramsEmpty && $tablePrefix = $this->grammar->getTablePrefix()) {
                    $query->where(
                        'table_name',
                        'like',
                        $tablePrefix . '%'
                    );
                }
            })
            ->when($exclude, function (QueryBuilder $query, $exclude) {
                $query->whereNotIn('table_name', array_map(
                        [$this, 'getTablePrefixName'],
                        explode(',', $exclude)
                    )
                );
            })
            ->when($likeTable, function (QueryBuilder $query, $likeTable) {
                $query->where(
                    'table_name',
                    'like',
                    $likeTable . '%'
                );
            })
            ->where('table_type', 'BASE TABLE')
            ->useWritePdo()
            ->select(...$columns)
            ->addSelect($addSelect)
            ->get()
            ->toArray();

        $removePrefix = '';
        if ($likeTable) {
            $removePrefixExplode = explode('_', $likeTable, 2);
            $removePrefix        = current($removePrefixExplode);

            if (count($removePrefixExplode) === 2) {
                $removePrefix .= '_';
            }
        }
        foreach ($results as $key => $item) {
            $item = (array)$item;
            // Re builder result
            $name = $this->removeTablePrefix($item['name']);

            $item['name'] = $name;

            if ($removePrefix) {
                $name = StringHelper::replaceFirst($removePrefix, '', $name);
            }
            $results[$name] = $item;
            unset($results[$key]);
        }
        unset($item);
        return $results;
    }

    /**
     * Check Mysql Database exists
     *
     * @return bool
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function checkDatabaseExists(): bool
    {
        $query = QueryBuilder::new($this->poolName, null, null);

        return $query->fromRaw('information_schema.SCHEMATA')
            ->where('SCHEMA_NAME', $this->getDatabaseName())
            ->exists();
    }

    /**
     * Drop all tables from the database.
     *
     * @return void
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function dropAllTables()
    {
        $tables = [];

        foreach ($this->getAllTables() as $row) {
            $row = (array)$row;

            $tables[] = reset($row);
        }

        if (empty($tables)) {
            return;
        }

        $this->disableForeignKeyConstraints();

        $this->getConnection()->statement(
            $this->grammar->compileDropAllTables($tables)
        );

        $this->enableForeignKeyConstraints();
    }

    /**
     * Drop all views from the database.
     *
     * @return void
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public function dropAllViews()
    {
        $views = [];

        foreach ($this->getAllViews() as $row) {
            $row = (array)$row;

            $views[] = reset($row);
        }

        if (empty($views)) {
            return;
        }

        $this->getConnection()->statement(
            $this->grammar->compileDropAllViews($views)
        );
    }

    /**
     * Get all of the table names for the database.
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     * @throws DbException
     */
    protected function getAllTables()
    {
        return $this->getConnection()->select(
            $this->grammar->compileGetAllTables()
        );
    }

    /**
     * Get all of the view names for the database.
     *
     * @return array
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    protected function getAllViews()
    {
        return $this->getConnection()->select(
            $this->grammar->compileGetAllViews()
        );
    }
}
