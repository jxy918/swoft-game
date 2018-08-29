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
 * db salve properties pool config
 * @Bean()
 */
class DbSlavePptConfig extends PoolProperties
{
    /**
     * the name of pool
     * @Value(name="${config.db.slave.name}")
     *
     * @var string
     */
    protected $name = '';

    /**
     * the maximum number of idle connections
     * @Value(name="${config.db.slave.maxIdel}")
     *
     * @var int
     */
    protected $maxIdel = 6;

    /**
     * the maximum number of active connections
     * @Value(name="${config.db.slave.maxActive}")
     *
     * @var int
     */
    protected $maxActive = 50;

    /**
     * the maximum number of wait connections
     * @Value(name="${config.db.slave.maxWait}")
     *
     * @var int
     */
    protected $maxWait = 100;

    /**
     * the time of connect timeout
     * @Value(name="${config.db.slave.timeout}")
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
     * @Value(name="${config.db.slave.uri}")
     *
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     * @Value(name="${config.db.slave.useProvider}")
     *
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     * @Value(name="${config.db.slave.balancer}")
     *
     * @var string
     */
    protected $balancer = 'random';

    /**
     * the default provider is consul provider
     * @Value(name="${config.db.slave.provider}")
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
