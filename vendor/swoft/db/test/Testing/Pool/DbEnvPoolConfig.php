<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Db\Testing\Pool;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\PoolProperties;

/**
 * db env pool config
 * @Bean()
 */
class DbEnvPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     * @Value(env="${DB_NAME}")
     *
     * @var string
     */
    protected $name = '';

    /**
     * the maximum number of idle connections
     * @Value(env="${DB_MAX_IDEL}")
     *
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     * @Value(env="${DB_MAX_ACTIVE}")
     *
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     * @Value(env="${DB_MAX_WAIT}")
     *
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     * @Value(env="${DB_TIMEOUT}")
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
     * @Value(env="${DB_URI}")
     *
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     * @Value(env="${DB_USE_PROVIDER}")
     *
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     * @Value(env="${DB_BALANCER}")
     *
     * @var string
     */
    protected $balancer = 'random';

    /**
     * the default provider is consul provider
     * @Value(env="${DB_PROVIDER}")
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
