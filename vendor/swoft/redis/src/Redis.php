<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Redis;

use Psr\SimpleCache\CacheInterface;
use Swoft\App;
use Swoft\Cache\CacheCoResult;
use Swoft\Cache\CacheDataResult;
use Swoft\Core\ResultInterface;
use Swoft\Pool\ConnectionInterface;
use Swoft\Pool\PoolInterface;
use Swoft\Redis\Pool\RedisPool;

/**
 * Psr 16 implement by Redis 
 * @method string getLastError()
 * @method bool clearLastError()
 */
class Redis implements CacheInterface
{
    /**
     * @var string
     */
    private $poolName = RedisPool::class;

    /**
     * Get the value related to the specified key
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return string|bool
     */
    public function get($key, $default = null)
    {
        $result = $this->call('get', [$key]);
        if ($result === false || $result === null) {
            return $default;
        }

        return $result;
    }

    /**
     * Set the string value in argument as value of the key.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl unit is seconds
     *
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
    {
        $ttl    = $this->getTtl($ttl);
        $params = ($ttl <= 0) ? [$key, $value] : [$key, $value, $ttl];

        return $this->call('set', $params);
    }

    /**
     * Remove specified keys.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key): bool
    {
        return (bool)$this->call('del', [$key]);
    }

    /**
     * Removes all entries from the current database.
     *
     * @return  bool  Always TRUE.
     */
    public function clear(): bool
    {
        return $this->call('flushDB', []);
    }

    /**
     * Returns the values of all specified keys.
     * For every key that does not hold a string value or does not exist,
     * the special value false is returned. Because of this, the operation never fails.
     *
     * @param iterable $keys
     * @param mixed    $default
     *
     * @return array|mixed
     */
    public function getMultiple($keys, $default = null)
    {
        $mgetResult = $this->call('mget', [$keys]);
        if ($mgetResult === false) {
            return $default;
        }
        $result = [];
        foreach ($mgetResult ?? [] as $key => $value) {
            $result[$keys[$key]] = $value;
        }

        return $result;
    }

    /**
     * Sets multiple key-value pairs in one atomic command.
     *
     * @param iterable $values
     * @param int      $ttl
     *
     * @return bool TRUE in case of success, FALSE in case of failure.
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $result = $this->call('mset', [$values]);

        return $result;
    }

    /**
     * Remove specified keys.
     *
     * @param iterable $keys
     *
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        return (bool)$this->call('del', [$keys]);
    }

    /**
     * Verify if the specified key exists.
     *
     * @param string $key
     *
     * @return  bool  If the key exists, return TRUE, otherwise return FALSE.
     */
    public function has($key): bool
    {
        return $this->call('exists', [$key]);
    }

    /**
     * defer call
     *
     * @param string $method
     * @param array  $params
     *
     * @return ResultInterface
     */
    public function deferCall(string $method, array $params)
    {
        $connectPool = App::getPool($this->poolName);

        /* @var $client RedisConnection */
        $client = $connectPool->getConnection();
        $client->setDefer();
        $result = $client->$method(...$params);

        return $this->getResult($client, $result);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    public function sIsMember($key, $value): bool
    {
        return (bool)$this->call('sIsMember', [$key, $value]);
    }

    /**
     * @param array $keys
     *
     * @return array|mixed
     */
    public function mget(array $keys)
    {
        return $this->getMultiple($keys, false);
    }

    /**
     * @param string $key
     * @param array  $hashKeys
     *
     * @return array
     */
    public function hMGet(string $key, array $hashKeys): array
    {
        $hMgetResult = $this->call('hMGet', [$key, $hashKeys]);
        if (!App::isCoContext()) {
            return $hMgetResult;
        }

        $result = [];
        foreach ($hMgetResult as $key => $value) {
            if (!isset($hashKeys[$key])) {
                continue;
            }

            $value = ($value === null) ? false : $value;
            $result[$hashKeys[$key]] = $value;
        }

        return $result;
    }

    /**
     * magic method
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->call($method, $arguments);
    }

    /**
     * call method by redis client
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function call(string $method, array $params)
    {
        /* @var PoolInterface $connectPool */
        $connectPool = App::getPool($this->poolName);
        /* @var ConnectionInterface $client */
        $connection = $connectPool->getConnection();
        $result     = $connection->$method(...$params);
        $connection->release(true);

        return $result;
    }

    /**
     * @param ConnectionInterface $connection
     * @param mixed               $result
     *
     * @return ResultInterface
     */
    private function getResult(ConnectionInterface $connection, $result)
    {
        if (App::isCoContext()) {
            return new CacheCoResult($result, $connection);
        }

        return new CacheDataResult($result, $connection);
    }

    /**
     * the ttl
     *
     * @param $ttl
     *
     * @return int
     */
    private function getTtl($ttl): int
    {
        return ($ttl === null) ? 0 : (int)$ttl;
    }
}
