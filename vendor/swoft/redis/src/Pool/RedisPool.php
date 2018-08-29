<?php

namespace Swoft\Redis\Pool;

use Swoft\App;
use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Pool;
use Swoft\Pool\ConnectionInterface;
use Swoft\Pool\ConnectionPool;
use Swoft\Redis\RedisConnection;
use Swoft\Redis\SyncRedisConnection;
use Swoft\Redis\Pool\Config\RedisPoolConfig;

/**
 * Redis pool
 *
 * @Pool()
 */
class RedisPool extends ConnectionPool
{
    /**
     * Config
     *
     * @Inject()
     * @var RedisPoolConfig
     */
    protected $poolConfig;

    /**
     * Create connection
     *
     * @return ConnectionInterface
     */
    public function createConnection(): ConnectionInterface
    {
        if (App::isCoContext()) {
            $redis = new RedisConnection($this);
        } else {
            $redis = new SyncRedisConnection($this);
        }

        $dbIndex = $this->poolConfig->getDb();
        $redis->select($dbIndex);

        return $redis;
    }
}
