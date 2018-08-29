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
 * properties and env
 *
 * @Bean
 */
class EnvAndPptPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     *
     * @Value(name="${config.test.test.name}", env="${TEST_NAME}")
     * @var string
     */
    protected $name = '';

    /**
     * the maximum number of idle connections
     *
     * @Value(name="${config.test.test.maxIdel}", env="${TEST_MAX_IDEL}")
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     *
     * @Value(name="${config.test.test.maxActive}", env="${TEST_MAX_ACTIVE}")
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     *
     * @Value(name="${config.test.test.maxWait}", env="${TEST_MAX_WAIT}")
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     *
     * @Value(name="${config.test.test.timeout}", env="${TEST_TIMEOUT}")
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
     * @Value(name="${config.test.test.uri}", env="${TEST_URI}")
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     *
     * @Value(name="${config.test.test.useProvider}", env="${TEST_USE_PROVIDER}")
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     *
     * @Value(name="${config.test.test.balancer}", env="${TEST_BALANCER}")
     * @var string
     */
    protected $balancer = 'random';

    /**
     * the default provider is consul provider
     *
     * @Value(name="${config.test.test.provider}", env="${TEST_PROVIDER}")
     * @var string
     */
    protected $provider = 'consul';
}
