<?php declare(strict_types=1);


namespace Swoft\Redis;

use Swoft\Bean\BeanFactory;
use Swoft\Redis\Connection\Connection;
use Swoft\Redis\Connection\ConnectionManager;
use Swoft\Redis\Exception\RedisException;
use Throwable;

/**
 * Class Redis
 *
 * @since 2.0
 *
 * @method static int append(string $key, string $value)
 * @method static array blPop(array $keys, int $timeout)
 * @method static array brPop(array $keys, int $timeout)
 * @method static string brpoplpush(string $srcKey, string $dstKey, int $timeout)
 * @method static string decr(string $key)
 * @method static int decrBy(string $key, int $value)
 * @method static mixed eval(string $script, array $args = [], int $numKeys = 0)
 * @method static mixed evalSha(string $scriptSha, array $args = [], int $numKeys = 0)
 * @method static bool exists(string $key)
 * @method static int geoAdd(string $key, float $longitude, float $latitude, string $member, ...$args)
 * @method static float geoDist(string $key, string $member1, string $member2, string $unit = 'm')
 * @method static array geohash(string $key, string ...$members)
 * @method static array geopos(string $key, string ...$members)
 * @method static mixed|bool get(string $key)
 * @method static int getBit(string $key, int $offset)
 * @method static int getOption(string $name)
 * @method static string getRange(string $key, int $start, int $end)
 * @method static string getSet(string $key, string $value)
 * @method static string hDel(string $key, string $hashKey1, string $hashKey2 = null, string $hashKeyN = null)
 * @method static bool hExists(string $key, string $hashKey)
 * @method static mixed hGet(string $key, string $hashKey)
 * @method static array hGetAll(string $key)
 * @method static int hIncrBy(string $key, string $hashKey, int $value)
 * @method static float hIncrByFloat(string $key, string $field, float $increment)
 * @method static array hKeys(string $key)
 * @method static int hLen(string $key)
 * @method static int hSet(string $key, string $hashKey, string $value)
 * @method static bool hSetNx(string $key, string $hashKey, string $value)
 * @method static array hVals(string $key)
 * @method static array hScan(string $key, int &$iterator, string $pattern = null, int $count = 0)
 * @method static int incr(string $key)
 * @method static int incrBy(string $key, int $value)
 * @method static float incrByFloat(string $key, float $increment)
 * @method static array info(string $option = null)
 * @method static string|bool lGet(string $key, int $index)
 * @method static int lInsert(string $key, int $position, string $pivot, string $value)
 * @method static string|bool lPop(string $key)
 * @method static int|bool lPush(string $key, string $value1, string $value2 = null, string $valueN = null)
 * @method static int|bool lPushx(string $key, string $value)
 * @method static bool lSet(string $key, int $index, string $value)
 * @method static int msetnx(array $array)
 * @method static bool persist(string $key)
 * @method static bool pExpire(string $key, int $ttl)
 * @method static bool pExpireAt(string $key, int $timestamp)
 * @method static bool psetex(string $key, int $ttl, $value)
 * @method static int pttl(string $key)
 * @method static string rPop(string $key)
 * @method static int|bool rPush(string $key, string $value1, string $value2 = null, string $valueN = null)
 * @method static int|bool rPushx(string $key, string $value)
 * @method static mixed rawCommand(string|array $nodeParams, string $command, mixed $arguments)
 * @method static bool renameNx(string $srcKey, string $dstKey)
 * @method static bool restore(string $key, int $ttl, string $value)
 * @method static string rpoplpush(string $srcKey, string $dstKey)
 * @method static int sAdd(string $key, string $value1, string $value2 = null, string $valueN = null)
 * @method static int sAddArray(string $key, array $valueArray)
 * @method static array sDiff(string $key1, string $key2, string $keyN = null)
 * @method static int sDiffStore(string $dstKey, string $key1, string $key2, string $keyN = null)
 * @method static array sInter(string $key1, string $key2, string $keyN = null)
 * @method static int|bool sInterStore(string $dstKey, string $key1, string $key2, string $keyN = null)
 * @method static array sMembers(string $key)
 * @method static bool sMove(string $srcKey, string $dstKey, string $member)
 * @method static string|bool sPop(string $key)
 * @method static string|array|bool sRandMember(string $key, int $count = null)
 * @method static array sUnion(string $key1, string $key2, string $keyN = null)
 * @method static int sUnionStore(string $dstKey, string $key1, string $key2, string $keyN = null)
 * @method static array|bool scan(int &$iterator, string $pattern = null, int $count = 0)
 * @method static mixed script(string|array $nodeParams, string $command, string $script)
 * @method static bool set(string $key, $value, int $timeout = null)
 * @method static int setBit(string $key, int $offset, int $value)
 * @method static string setRange(string $key, int $offset, $value)
 * @method static int setex(string $key, int $ttl, $value)
 * @method static bool setnx(string $key, $value)
 * @method static array sort(string $key, array $option = null)
 * @method static array sScan(string $key, int &$iterator, string $pattern = null, int $count = 0)
 * @method static int strlen(string $key)
 * @method static int ttl(string $key)
 * @method static int type(string $key)
 * @method static void unwatch()
 * @method static void watch(string $key)
 * @method static int zCard(string $key)
 * @method static int zCount(string $key, int $start, int $end)
 * @method static float zIncrBy(string $key, float $value, string $member)
 * @method static int zLexCount(string $key, int $min, int $max)
 * @method static array zRange(string $key, int $start, int $end, bool $withscores = null)
 * @method static array zRangeByLex(string $key, int $min, int $max, int $offset = null, int $limit = null)
 * @method static array zRangeByScore(string $key, string $start, string $end, array $options = [])
 * @method static int zRank(string $key, string $member)
 * @method static array zRemRangeByLex(string $key, int $min, int $max)
 * @method static array zRevRange(string $key, int $start, int $end, bool $withscore = null)
 * @method static array zRevRangeByLex(string $key, int $min, int $max, int $offset = null, int $limit = null)
 * @method static array zRevRangeByScore(string $key, int $start, int $end, array $options = [])
 * @method static int zRevRank(string $key, string $member)
 * @method static float zScore(string $key, mixed $member)
 * @method static array zScan(string $key, int &$iterator, string $pattern = null, int $count = 0)
 * @method static int del(string $key1, string $key2 = null, string $key3 = null)
 * @method static bool expire(string $key, int $ttl)
 * @method static array keys(string $pattern)
 * @method static int lLen(string $key)
 * @method static string|bool lIndex(string $key, int $index)
 * @method static array lRange(string $key, int $start, int $end)
 * @method static int|bool lRem(string $key, string $value, int $count)
 * @method static array|bool lTrim(string $key, int $start, int $stop)
 * @method static bool rename(string $srcKey, string $dstKey)
 * @method static int sCard(string $key)
 * @method static bool sIsMember(string $key, string $value)
 * @method static int sRem(string $key, string $member1, string $member2 = null, string $memberN = null)
 * @method static int zRem(string $key, string $member1, string $member2 = null, string $memberN = null)
 * @method static int zRemRangeByRank(string $key, int $start, int $end)
 * @method static int zRemRangeByScore(string $key, float|string $start, float|string $end)
 * @method static int zInterStore(string $Output, array $ZSetKeys, array $Weights = null, string $aggregateFunction = 'SUM')
 * @method static int zUnionStore(string $Output, array $ZSetKeys, array $Weights = null, string $aggregateFunction = 'SUM')
 * @method static array hMGet(string $key, array $keys)
 * @method static bool hMSet(string $key, array $keyValues)
 * @method static int zAdd(string $key, array $scoreValues)
 * @method static array mget(array $keys)
 * @method static bool mset(array $keyValues, int $ttl = 0)
 * @method static array pipeline(callable $callback)
 * @method static array transaction(callable $callback)
 * @method static mixed call(callable $callback, bool $reconnect = false)
 */
class Redis
{
    /**
     * @param string $pool
     *
     * @return Connection
     * @throws RedisException
     */
    public static function connection(string $pool = Pool::DEFAULT_POOL): Connection
    {
        try {
            /* @var ConnectionManager $conManager */
            $conManager = BeanFactory::getBean(ConnectionManager::class);

            /* @var Pool $redisPool */
            $redisPool  = BeanFactory::getBean($pool);
            $connection = $redisPool->getConnection();

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
        return $connection;
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     * @throws RedisException
     */
    public static function __callStatic(string $method, array $arguments)
    {
        $connection = self::connection();
        return $connection->{$method}(...$arguments);
    }
}
