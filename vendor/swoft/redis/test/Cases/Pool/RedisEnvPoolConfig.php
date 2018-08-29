<?php

namespace SwoftTest\Redis\Pool;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\PoolProperties;

/**
 * the redis env config
 * @Bean()
 */
class RedisEnvPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     * @Value(env="${REDIS_NAME}")
     *
     * @var string
     */
    protected $name = '';

    /**
     * the maximum number of idle connections
     * @Value(env="${REDIS_MAX_IDEL}")
     *
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     * @Value(env="${REDIS_MAX_ACTIVE}")
     *
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     * @Value(env="${REDIS_MAX_WAIT}")
     *
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     * @Value(env="${REDIS_TIMEOUT}")
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
     * @Value(env="${REDIS_URI}")
     *
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     * @Value(env="${REDIS_USE_PROVIDER}")
     *
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     * @Value(env="${REDIS_BALANCER}")
     *
     * @var string
     */
    protected $balancer = 'random';

    /**
     * the default provider is consul provider
     * @Value(env="${REDIS_PROVIDER}")
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
