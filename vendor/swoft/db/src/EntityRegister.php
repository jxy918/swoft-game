<?php declare(strict_types=1);


namespace Swoft\Db;

use Swoft\Db\Exception\DbException;

/**
 * Class EntityRegister
 *
 * @since 2.0
 */
class EntityRegister
{
    /**
     * Entity array
     *
     * @var array
     *
     * @example
     * [
     *     'entityClassName' => [
     *         'table' => 'table',
     *         'pool' => 'pool',
     *         'id' => [
     *             'attr' => 'attrName',
     *             'incrementing' => true
     *         ]
     *     ]
     * ]
     */
    private static $entity = [];

    /**
     * Columns
     *
     * @var array
     *
     * @example
     * [
     *     'entityClassName' =>[
     *         'mapping' => [
     *             'attrName' => [
     *                 'column' => 'columnName',
     *                 'pro' => 'proName',
     *                 'hidden' => false,
     *                 'type' => 'int',
     *             ]
     *         ],
     *         'reverse' => [
     *             'columnName' => [
     *                 'attr' => 'attrName',
     *                 'pro' => 'proName',
     *                 'hidden' => false,
     *                 'type' => 'int',
     *             ]
     *         ]
     *     ]
     * ]
     */
    private static $columns = [];

    /**
     * Register `Entity`
     *
     * @param string $className
     * @param string $table
     * @param string $pool
     */
    public static function registerEntity(string $className, string $table, string $pool): void
    {
        self::$entity[$className] = [
            'table' => $table,
            'pool'  => $pool,
        ];
    }

    /**
     * Register `Id`
     *
     * @param string $className
     * @param string $attrName
     * @param bool incrementing
     *
     * @throws DbException
     */
    public static function registerId(string $className, string $attrName, bool $incrementing): void
    {
        if (!isset(self::$entity[$className])) {
            throw new DbException(sprintf('%s must be `@Entity` to use `@Id`', $className));
        }

        self::$entity[$className]['id']['attr']         = $attrName;
        self::$entity[$className]['id']['incrementing'] = $incrementing;
    }

    /**
     * Register `Column`
     *
     * @param string $className
     * @param string $attrName
     * @param string $column
     * @param string $pro
     * @param bool   $hidden
     * @param string $type
     *
     * @throws DbException
     */
    public static function registerColumn(
        string $className,
        string $attrName,
        string $column,
        string $pro,
        bool $hidden,
        string $type
    ): void {
        if (!isset(self::$entity[$className])) {
            throw new DbException(sprintf('%s must be `@Entity` to use `@Column`', $className));
        }

        if (isset(self::$columns[$className]['reverse'][$column])) {
            throw new DbException(sprintf('The `%s` name of `@Column` has exist in %s', $className, $column));
        }

        self::$columns[$className]['mapping'][$attrName] = [
            'column' => $column,
            'pro'    => $pro,
            'hidden' => $hidden,
            'type'   => $type,
        ];

        self::$columns[$className]['reverse'][$column] = [
            'attr'   => $attrName,
            'pro'    => $pro,
            'hidden' => $hidden,
            'type'   => $type,
        ];
    }

    /**
     * Get table
     *
     * @param string $className
     *
     * @return string
     * @throws DbException
     */
    public static function getTable(
        string $className
    ): string {
        $table = self::$entity[$className]['table'] ?? '';
        if (empty($table)) {
            throw new DbException(sprintf('The table of `%s` entity is not defined or empty', $className));
        }

        return $table;
    }

    /**
     * Get pool
     *
     * @param string $className
     *
     * @return string
     * @throws DbException
     */
    public static function getPool(string $className): string
    {
        $pool = self::$entity[$className]['pool'] ?? '';
        if (empty($pool)) {
            throw new DbException(sprintf('The pool of `%s` entity is not defined or empty', $className));
        }

        return $pool;
    }

    /**
     * Get pk id
     *
     * @param string $className
     *
     * @return string
     * @throws DbException
     */
    public static function getId(string $className): string
    {
        $idAttrName = self::$entity[$className]['id']['attr'] ?? '';
        if (empty($idAttrName)) {
            throw new DbException(sprintf('The `@Id` of `%s` entity is not defined', $className));
        }

        $idColumn = self::$columns[$className]['mapping'][$idAttrName]['column'] ?? '';
        if (empty($idColumn)) {
            throw new DbException(
                sprintf('`%s` property must be define `@Column` in %s', $idAttrName, $className)
            );
        }

        return $idColumn;
    }

    /**
     * Get id is incrementing
     *
     * @param string $className
     *
     * @return bool
     */
    public static function getIdIncrementing(string $className): bool
    {
        return self::$entity[$className]['id']['incrementing'] ?? true;
    }

    /**
     * Get column mapping
     *
     * @param string $className
     *
     * @return array
     */
    public static function getMapping(string $className): array
    {
        return self::$columns[$className]['mapping'] ?? [];
    }

    /**
     * Get column mapping
     *
     * @param string $className
     * @param string $column
     *
     * @return array
     */
    public static function getReverseMappingByColumn(string $className, string $column): array
    {
        return self::$columns[$className]['reverse'][$column] ?? [];
    }
}
