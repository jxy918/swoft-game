<?php declare(strict_types=1);


namespace Swoft\Db;

use function bean;
use Closure;
use Generator;
use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\Exception\ConnectionPoolException;
use Swoft\Db\Connection\Connection;
use Swoft\Db\Connection\ConnectionManager;
use Swoft\Db\Contract\ConnectionInterface;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Query\Builder;
use Swoft\Db\Query\Expression;
use Throwable;

/**
 * Class Db
 *
 * @see   Connection
 * @since 2.0
 *
 * @method static mixed selectOne(string $query, $bindings = [], $useReadPdo = true)
 * @method static array select(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static Generator cursor(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static bool insert(string $query, array $bindings = [])
 * @method static int update(string $query, array $bindings = [])
 * @method static int delete(string $query, array $bindings = [])
 * @method static bool statement(string $query, array $bindings = [])
 * @method static int affectingStatement(string $query, array $bindings = [])
 * @method static bool unprepared(string $query)
 * @method static mixed transaction(Closure $callback, $attempts = 1)
 * @method static void beginTransaction()
 * @method static void commit()
 * @method static ConnectionInterface db(string $dbname)
 * @method static void rollBack(int $toLevel = null)
 */
class DB
{
    /**
     * Supported methods
     *
     * @var array
     */
    private static $passthru = [
        'selectOne',
        'select',
        'cursor',
        'insert',
        'update',
        'delete',
        'statement',
        'affectingStatement',
        'unprepared',
        'transaction',
        'beginTransaction',
        'commit',
        'rollBack',
        'db'
    ];

    /**
     * @param string $name
     *
     * @return Connection
     * @throws DbException
     */
    public static function connection(string $name = Pool::DEFAULT_POOL): Connection
    {
        try {
            /* @var ConnectionManager $cm */
            $cm = bean(ConnectionManager::class);
            if ($cm->isTransaction($name)) {
                return $cm->getTransactionConnection($name);
            }

            return self::getConnectionFromPool($name);
        } catch (Throwable $e) {
            throw new DbException(
                sprintf('Pool error is %s file=%s line=%d', $e->getMessage(), $e->getFile(), $e->getLine())
            );
        }
    }

    /**
     * @param string $table
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public static function table(string $table): Builder
    {
        return self::query()->from($table);
    }

    /**
     * @param string $name
     *
     * @return Builder
     * @throws ContainerException
     * @throws DbException
     * @throws ReflectionException
     */
    public static function query(string $name = Pool::DEFAULT_POOL): Builder
    {
        return Builder::new($name, null, null);
    }

    /**
     * Call method
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @throws DbException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (!in_array($name, self::$passthru)) {
            throw new DbException(sprintf('Method(%s) is not exist!', $name));
        }

        $connection = self::connection();
        return $connection->$name(...$arguments);
    }

    /**
     * Get a new raw query expression.
     *
     * @param $value
     *
     * @return Expression
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function raw($value): Expression
    {
        return Expression::new($value);
    }

    /**
     * Get connection from pool
     *
     * @param string $name
     *
     * @return Connection
     * @throws ContainerException
     * @throws ConnectionPoolException
     * @throws Throwable
     */
    private static function getConnectionFromPool(string $name): Connection
    {
        $pool = bean($name);
        if (!$pool instanceof Pool) {
            throw new DbException(sprintf('%s is not instance of pool', $name));
        }

        /* @var ConnectionManager $conManager */
        $conManager = BeanFactory::getBean(ConnectionManager::class);
        $connection = $pool->getConnection();
        $connection->setPoolName($name);

        $connection->setRelease(true);
        $conManager->setOrdinaryConnection($connection, $name);
        return $connection;
    }
}
