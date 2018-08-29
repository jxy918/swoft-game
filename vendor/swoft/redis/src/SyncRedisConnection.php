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

use Swoft\Redis\Exception\RedisException;
use Swoft\Redis\Pool\Config\RedisPoolConfig;

/**
 * Sync redis connection
 */
class SyncRedisConnection extends AbstractRedisConnection
{
    /**
     * @return void
     * @throws RedisException
     */
    public function createConnection()
    {
        /* @var RedisPoolConfig $poolConfig */
        $poolConfig = $this->pool->getPoolConfig();
        $prefix     = $poolConfig->getPrefix();
        $serialize  = $poolConfig->getSerialize();
        $serialize  = ((int)$serialize == 0) ? false : true;
        // init
        $redis = $this->initRedis();
        if ($serialize) {
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
        }

        if (!empty($prefix) && is_string($prefix)) {
            $redis->setOption(\Redis::OPT_PREFIX, $prefix);
        }
        $this->connection = $redis;
    }

    /**
     * @param bool $defer
     *
     * @throws RedisException
     */
    public function setDefer($defer = true)
    {
        throw new RedisException('not support');
    }

    /**
     * @param string $host
     * @param int    $port
     * @param int    $timeout
     *
     * @return \Redis
     * @throws RedisException
     */
    protected function getConnectRedis(string $host, int $port, int $timeout): \Redis
    {
        $redis  = new \Redis();
        $result = $redis->connect($host, $port, $timeout);
        if ($result == false) {
            $error = sprintf('Redis connection failure host=%s port=%d', $host, $port);
            throw new RedisException($error);
        }

        return $redis;
    }

}
