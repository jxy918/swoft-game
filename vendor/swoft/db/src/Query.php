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

/**
 * Query
 */
class Query
{
    /**
     * @param string $tableName
     * @param string $alias
     *
     * @return QueryBuilder
     */
    public static function table(string $tableName, string $alias = null): QueryBuilder
    {
        $query = new QueryBuilder();
        $query = $query->table($tableName, $alias);

        return $query;
    }
}
