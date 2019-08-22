<?php declare(strict_types=1);


namespace Swoft\Db;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Schema\Builder;
use Swoft\Db\Schema\Grammars\Grammar;
use function in_array;

/**
 * @method static Builder create(string $table, \Closure $callback, bool $ifNotExist = false)
 * @method static Builder createIfNotExists(string $table, \Closure $callback)
 * @method static Builder drop(string $table)
 * @method static Builder dropIfExists(string $table)
 * @method static Builder table(string $table, \Closure $callback)
 * @method static Builder rename(string $from, string $to)
 * @method static Builder disableForeignKeyConstraints()
 * @method static Builder enableForeignKeyConstraints()
 *
 * @see   Builder
 *
 * Class Schema
 *
 * @since 2.0
 */
class Schema
{
    /**
     * Supported methods
     *
     * @var array
     */
    private static $passthru = [
        'create',
        'createIfNotExists',
        'drop',
        'dropIfExists',
        'table',
        'rename',
        'disableForeignKeyConstraints',
        'enableForeignKeyConstraints',
    ];

    /**
     * @param string  $pool
     * @param Grammar $grammar
     *
     * @return Builder
     * @throws DbException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function getSchemaBuilder(string $pool = Pool::DEFAULT_POOL, Grammar $grammar = null)
    {
        return Builder::new($pool, $grammar);
    }

    /**
     * Call method
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @throws DbException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (!in_array($name, self::$passthru)) {
            throw new DbException(sprintf('Schema not support method(%s)!', $name));
        }

        $schema = self::getSchemaBuilder();
        return $schema->$name(...$arguments);
    }
}
