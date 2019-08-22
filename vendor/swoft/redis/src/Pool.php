<?php declare(strict_types=1);


namespace Swoft\Redis;

use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\AbstractPool;
use Swoft\Connection\Pool\Contract\ConnectionInterface;
use Swoft\Redis\Connection\Connection;
use Swoft\Redis\Connection\ConnectionManager;
use Swoft\Redis\Exception\RedisException;
use Throwable;

/**
 * Class Pool
 *
 * @since 2.0
 *
 * @method int append(string $key, string $value)
 * @method array blPop(array $keys, int $timeout)
 * @method array brPop(array $keys, int $timeout)
 * @method string brpoplpush(string $srcKey, string $dstKey, int $timeout)
 * @method string decr(string $key)
 * @method int decrBy(string $key, int $value)
 * @method mixed eval(string $script, array $args = [], int $numKeys = 0)
 * @method mixed evalSha(string $scriptSha, array $args = [], int $numKeys = 0)
 * @method bool exists(string $key)
 * @method int geoAdd(string $key, float $longitude, float $latitude, string $member, ...$args)
 * @method float geoDist(string $key, string $member1, string $member2, string $unit = 'm')
 * @method array geohash(string $key, string ...$members)
 * @method array geopos(string $key, string ...$members)
 * @method mixed|bool get(string $key)
 * @method int getBit(string $key, int $offset)
 * @method int getOption(string $name)
 * @method string getRange(string $key, int $start, int $end)
 * @method string getSet(string $key, string $value)
 * @method string hDel(string $key, string $hashKey1, string $hashKey2 = null, string $hashKeyN = null)
 * @method bool hExists(string $key, string $hashKey)
 * @method array hGet(string $key, string $hashKey)
 * @method array hGetAll(string $key)
 * @method int hIncrBy(string $key, string $hashKey, int $value)
 * @method float hIncrByFloat(string $key, string $field, float $increment)
 * @method array hKeys(string $key)
 * @method int hLen(string $key)
 * @method int hSet(string $key, string $hashKey, string $value)
 * @method bool hSetNx(string $key, string $hashKey, string $value)
 * @method array hVals(string $key)
 * @method array hScan(string $key, int &$iterator, string $pattern = null, int $count = 0)
 * @method int incr(string $key)
 * @method int incrBy(string $key, int $value)
 * @method float incrByFloat(string $key, float $increment)
 * @method array info(string $option = null)
 * @method string|bool lGet(string $key, int $index)
 * @method int lInsert(string $key, int $position, string $pivot, string $value)
 * @method string|bool lPop(string $key)
 * @method int|bool lPush(string $key, string $value1, string $value2 = null, string $valueN = null)
 * @method int|bool lPushx(string $key, string $value)
 * @method bool lSet(string $key, int $index, string $value)
 * @method int msetnx(array $array)
 * @method bool persist(string $key)
 * @method bool pExpire(string $key, int $ttl)
 * @method bool pExpireAt(string $key, int $timestamp)
 * @method bool psetex(string $key, int $ttl, $value)
 * @method int pttl(string $key)
 * @method string rPop(string $key)
 * @method int|bool rPush(string $key, string $value1, string $value2 = null, string $valueN = null)
 * @method int|bool rPushx(string $key, string $value)
 * @method mixed rawCommand(...$args)
 * @method bool renameNx(string $srcKey, string $dstKey)
 * @method bool restore(string $key, int $ttl, string $value)
 * @method string rpoplpush(string $srcKey, string $dstKey)
 * @method int sAdd(string $key, string $value1, string $value2 = null, string $valueN = null)
 * @method int sAddArray(string $key, array $valueArray)
 * @method array sDiff(string $key1, string $key2, string $keyN = null)
 * @method int sDiffStore(string $dstKey, string $key1, string $key2, string $keyN = null)
 * @method array sInter(string $key1, string $key2, string $keyN = null)
 * @method int|bool sInterStore(string $dstKey, string $key1, string $key2, string $keyN = null)
 * @method array sMembers(string $key)
 * @method bool sMove(string $srcKey, string $dstKey, string $member)
 * @method string|bool sPop(string $key)
 * @method string|array|bool sRandMember(string $key, int $count = null)
 * @method array sUnion(string $key1, string $key2, string $keyN = null)
 * @method int sUnionStore(string $dstKey, string $key1, string $key2, string $keyN = null)
 * @method array|bool scan(int &$iterator, string $pattern = null, int $count = 0)
 * @method mixed script(string|array $nodeParams, string $command, string $script)
 * @method bool set(string $key, $value, int $timeout = null)
 * @method int setBit(string $key, int $offset, int $value)
 * @method string setRange(string $key, int $offset, $value)
 * @method int setex(string $key, int $ttl, $value)
 * @method bool setnx(string $key, $value)
 * @method array sort(string $key, array $option = null)
 * @method array sScan(string $key, int &$iterator, string $pattern = null, int $count = 0)
 * @method int strlen(string $key)
 * @method int ttl(string $key)
 * @method int type(string $key)
 * @method void unwatch()
 * @method void watch(string $key)
 * @method int zCard(string $key)
 * @method int zCount(string $key, int $start, int $end)
 * @method float zIncrBy(string $key, float $value, string $member)
 * @method int zLexCount(string $key, int $min, int $max)
 * @method array zRange(string $key, int $start, int $end, bool $withscores = null)
 * @method array zRangeByLex(string $key, int $min, int $max, int $offset = null, int $limit = null)
 * @method array zRangeByScore(string $key, string $start, string $end, array $options = [])
 * @method int zRank(string $key, string $member)
 * @method array zRemRangeByLex(string $key, int $min, int $max)
 * @method array zRevRange(string $key, int $start, int $end, bool $withscore = null)
 * @method array zRevRangeByLex(string $key, int $min, int $max, int $offset = null, int $limit = null)
 * @method array zRevRangeByScore(string $key, int $start, int $end, array $options = [])
 * @method int zRevRank(string $key, string $member)
 * @method float zScore(string $key, mixed $member)
 * @method array zScan(string $key, int &$iterator, string $pattern = null, int $count = 0)
 * @method int del(string $key1, string $key2 = null, string $key3 = null)
 * @method bool expire(string $key, int $ttl)
 * @method array keys(string $pattern)
 * @method int lLen(string $key)
 * @method string|bool lIndex(string $key, int $index)
 * @method array lRange(string $key, int $start, int $end)
 * @method int|bool lRem(string $key, string $value, int $count)
 * @method array|bool lTrim(string $key, int $start, int $stop)
 * @method bool rename(string $srcKey, string $dstKey)
 * @method int sCard(string $key)
 * @method bool sIsMember(string $key, string $value)
 * @method int sRem(string $key, string $member1, string $member2 = null, string $memberN = null)
 * @method int zRem(string $key, string $member1, string $member2 = null, string $memberN = null)
 * @method int zRemRangeByRank(string $key, int $start, int $end)
 * @method int zRemRangeByScore(string $key, float|string $start, float|string $end)
 * @method int zInterStore(string $Output, array $ZSetKeys, array $Weights = null, string $aggregateFunction = 'SUM')
 * @method int zUnionStore(string $Output, array $ZSetKeys, array $Weights = null, string $aggregateFunction = 'SUM')
 * @method array hMGet(string $key, array $keys)
 * @method bool hMSet(string $key, array $keyValues)
 * @method int zAdd(string $key, array $scoreValues)
 * @method array mget(array $keys)
 * @method bool mset(array $keyValues, int $ttl = 0)
 * @method array pipeline(callable $callback)
 * @method array transaction(callable $callback)
 * @method mixed call(callable $callback, bool $reconnect = false)
 */
class Pool extends AbstractPool
{
    /**
     * Default pool
     */
    const DEFAULT_POOL = 'redis.pool';

    /**
     * @var RedisDb
     */
    protected $redisDb;

    /**
     * @return ConnectionInterface
     * @throws Exception\RedisException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function createConnection(): ConnectionInterface
    {
        return $this->redisDb->createConnection($this);
    }

    /**
     * call magic method
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return Connection
     * @throws RedisException
     */
    public function __call(string $name, array $arguments)
    {
        try {
            /* @var ConnectionManager $conManager */
            $conManager = BeanFactory::getBean(ConnectionManager::class);

            $connection = $this->getConnection();

            $connection->setRelease(true);
            $conManager->setConnection($connection);
        } catch (Throwable $e) {
            throw new RedisException(
                sprintf('Pool error is %s file=%s line=%d', $e->getMessage(), $e->getFile(), $e->getLine())
            );
        }

        // Not instanceof Connection
        if (!$connection instanceof Connection) {
            throw new RedisException(
                sprintf('%s is not instanceof %s', get_class($connection), Connection::class)
            );
        }

        return $connection->{$name}(...$arguments);
    }

    /**
     * @return RedisDb
     */
    public function getRedisDb(): RedisDb
    {
        return $this->redisDb;
    }
}
