<?php

namespace Swoft\Redis\Pool\Config;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\PoolProperties;

/**
 * the pool config of redis
 *
 * @Bean()
 */
class RedisPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     *
     * @Value(name="${config.cache.redis.name}", env="${REDIS_NAME}")
     * @var string
     */
    protected $name = '';

    /**
     * Minimum active number of connections
     *
     * @Value(name="${config.cache.redis.minActive}", env="${REDIS_MIN_ACTIVE}")
     * @var int
     */
    protected $minActive = 5;

    /**
     * the maximum number of active connections
     *
     * @Value(name="${config.cache.redis.maxActive}", env="${REDIS_MAX_ACTIVE}")
     * @var int
     */
    protected $maxActive = 10;

    /**
     * the maximum number of wait connections
     *
     * @Value(name="${config.cache.redis.maxWait}", env="${REDIS_MAX_WAIT}")
     * @var int
     */
    protected $maxWait = 20;

    /**
     * Maximum waiting time
     *
     * @Value(name="${config.cache.redis.maxWaitTime}", env="${REDIS_MAX_WAIT_TIME}")
     * @var int
     */
    protected $maxWaitTime = 3;

    /**
     * Maximum idle time
     *
     * @Value(name="${config.cache.redis.maxIdleTime}", env="${REDIS_MAX_IDLE_TIME}")
     * @var int
     */
    protected $maxIdleTime = 60;

    /**
     * the time of connect timeout
     *
     * @Value(name="${config.cache.redis.timeout}", env="${REDIS_TIMEOUT}")
     * @var int
     */
    protected $timeout = 3;

    /**
     * the addresses of connection
     *
     * <pre>
     * [
     *  '127.0.0.1:88',
     *  '127.0.0.1:88'
     * ]
     * </pre>
     *
     * @Value(name="${config.cache.redis.uri}", env="${REDIS_URI}")
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     *
     * @Value(name="${config.cache.redis.useProvider}", env="${REDIS_USE_PROVIDER}")
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     *
     * @Value(name="${config.cache.redis.balancer}", env="${REDIS_BALANCER}")
     * @var string
     */
    protected $balancer = '';

    /**
     * the default provider is consul provider
     *
     * @Value(name="${config.cache.redis.provider}", env="${REDIS_PROVIDER}")
     * @var string
     */
    protected $provider = '';

    /**
     * the index of redis db
     *
     * @Value(name="${config.cache.redis.db}", env="${REDIS_DB}")
     * @var int
     */
    protected $db = 0;

    /**
     * @Value(name="${config.cache.redis.prefix}", env="${REDIS_PREFIX}")
     * @var string
     */
    protected $prefix = '';

    /**
     * Whether to be serialized
     *
     * @Value(name="${config.cache.redis.serialize}", env="${REDIS_SERIALIZE}")
     * @var int
     */
    protected $serialize = 0;

    /**
     * @return int
     */
    public function getSerialize(): int
    {
        return $this->serialize;
    }

    /**
     * @return int
     */
    public function getDb(): int
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
