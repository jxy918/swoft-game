<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace SwoftTest\Pool;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Pool\PoolProperties;

/**
 * the config of env
 *
 * @Bean
 */
class EnvPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     *
     * @Value(env="${TEST_NAME}")
     * @var string
     */
    protected $name = '';

    /**
     * the maximum number of idle connections
     *
     * @Value(env="${TEST_MAX_IDEL}")
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     *
     * @Value(env="${TEST_MAX_ACTIVE}")
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     *
     * @Value(env="${TEST_MAX_WAIT}")
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     *
     * @Value(env="${TEST_TIMEOUT}")
     * @var int
     */
    protected $timeout = 200;

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
     * @Value(env="${TEST_URI}")
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     *
     * @Value(env="${TEST_USE_PROVIDER}")
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     *
     * @Value(env="${TEST_BALANCER}")
     * @var string
     */
    protected $balancer = 'random';

    /**
     * the default provider is consul provider
     *
     * @Value(env="${TEST_PROVIDER}")
     * @var string
     */
    protected $provider = 'consul';
}
