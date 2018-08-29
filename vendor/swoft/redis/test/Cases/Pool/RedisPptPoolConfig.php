<?php

namespace SwoftTest\Redis\Pool;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\PoolProperties;

/**
 * the redis properties
 * @Bean()
 */
class RedisPptPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     * @Value(name="${config.cache.redis.name}")
     *
     * @var string
     */
    protected $name = '';

    /**
     * the maximum number of idle connections
     * @Value(name="${config.cache.redis.maxIdel}")
     *
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     * @Value(name="${config.cache.redis.maxActive}")
     *
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     * @Value(name="${config.cache.redis.maxWait}")
     *
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     * @Value(name="${config.cache.redis.timeout}")
     *
     * @var int
     */
    protected $timeout = 200;

    /**
     * the addresses of connection
     * <pre>
     * [
     *  '127.0.0.1:88',
     *  '127.0.0.1:88'
     * ]
     * </pre>
     * @Value(name="${config.cache.redis.uri}")
     *
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     * @Value(name="${config.cache.redis.useProvider}")
     *
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     * @Value(name="${config.cache.redis.balancer}")
     *
     * @var string
     */
    protected $balancer = 'random';

    /**
     * the default provider is consul provider
     * @Value(name="${config.cache.redis.provider}")
     *
     * @var string
     */
    protected $provider = 'consul';

    /**
     * @return int
     */
    public function getMaxIdel(): int
    {
        return $this->maxIdel;
    }
}
