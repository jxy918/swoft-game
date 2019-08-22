<?php declare(strict_types=1);


namespace Swoft\Devtool\Model\Dao;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Schema\Builder;

/**
 * @Bean()
 *
 * @since 2.0
 */
class SchemaDao
{
    /**
     * Get database schema columns
     *
     * @param string $pool
     * @param string $table
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     * @throws DbException
     */
    public function getColumnsSchema(string $pool, string $table): array
    {
        $schemaBuilder = Builder::new($pool);
        $columnsDetail = $schemaBuilder->getColumnsDetail($table);

        foreach ($columnsDetail as &$column) {
            $originPHPType = $schemaBuilder->convertType($column['type']);

            // if is null able
            $nullable              = $column['nullable'] === 'YES';
            $column['is_nullable'] = $nullable;

            $column['phpType']       = $originPHPType . ($nullable ? '|null' : '');
            $column['originPHPType'] = $originPHPType;
        }
        unset($column);
        
        return $columnsDetail;
    }

    /**
     * Get Table name and comment
     *
     * @param string $pool
     * @param string $table
     *
     * @param string $exclude
     * @param string $tablePrefix
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     * @throws DbException
     */
    public function getTableSchema(string $pool, string $table, string $exclude, string $tablePrefix): array
    {
        $schemaBuilder = Builder::new($pool);
        $tableSchema   = $schemaBuilder->getTableSchema($table, [], $exclude, $tablePrefix);
        return $tableSchema;
    }
}
