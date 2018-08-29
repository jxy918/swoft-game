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
 * config by properties
 *
 * @Bean
 */
class PropertyPoolConfig extends PoolProperties
{
    /**
     * the name of pool
     *
     * @Value(name="${config.test.test.name}")
     * @var string
     */
    protected $name = '';

    /**
     * the maximum number of idle connections
     *
     * @Value(name="${config.test.test.maxIdel}")
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     *
     * @Value(name="${config.test.test.maxActive}")
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     *
     * @Value(name="${config.test.test.maxWait}")
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     *
     * @Value(name="${config.test.test.timeout}")
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
     * @Value(name="${config.test.test.uri}")
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     *
     * @Value(name="${config.test.test.useProvider}")
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     *
     * @Value(name="${config.test.test.balancer}")
     * @var string
     */
    protected $balancer = 'random';

    /**
     * the default provider is consul provider
     *
     * @Value(name="${config.test.test.provider}")
     * @var string
     */
    protected $provider = 'consul';
}
