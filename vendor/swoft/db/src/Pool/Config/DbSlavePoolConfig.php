<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Pool\Config;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Value;
use Swoft\Db\Driver\Driver;

/**
 * the slave config of database
 *
 * @Bean()
 */
class DbSlavePoolConfig extends DbPoolProperties
{
    /**
     * @Value(name="${config.db.slave.name}", env="${DB_SLAVE_NAME}")
     * @var string
     */
    protected $name = '';

    /**
     * @Value(name="${config.db.slave.minActive}", env="${DB_SLAVE_MIN_ACTIVE}")
     * @var int
     */
    protected $minActive = 5;

    /**
     * @Value(name="${config.db.slave.maxActive}", env="${DB_SLAVE_MAX_ACTIVE}")
     * @var int
     */
    protected $maxActive = 10;

    /**
     * @Value(name="${config.db.slave.maxWait}", env="${DB_SLAVE_MAX_WAIT}")
     * @var int
     */
    protected $maxWait = 20;

    /**
     * @Value(name="${config.db.slave.maxWaitTime}", env="${DB_SLAVE_MAX_WAIT_TIME}")
     * @var int
     */
    protected $maxWaitTime = 3;

    /**
     * @Value(name="${config.db.slave.maxIdleTime}", env="${DB_SLAVE_MAX_IDLE_TIME}")
     * @var int
     */
    protected $maxIdleTime = 60;

    /**
     * @Value(name="${config.db.slave.timeout}", env="${DB_SLAVE_TIMEOUT}")
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
     * @Value(name="${config.db.slave.uri}", env="${DB_SLAVE_URI}")
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     *
     * @Value(name="${config.db.slave.useProvider}", env="${DB_SLAVE_USE_PROVIDER}")
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     *
     * @Value(name="${config.db.slave.balancer}", env="${DB_SLAVE_BALANCER}")
     * @var string
     */
    protected $balancer = 'random';

    /**
     * the default provider is consul provider
     *
     * @Value(name="${config.db.slave.provider}", env="${DB_SLAVE_PROVIDER}")
     * @var string
     */
    protected $provider = 'consul';

    /**
     * the default driver is mysql
     *
     * @Value(name="${config.db.slave.driver}", env="${DB_SLAVE_DRIVER}")
     * @var string
     */
    protected $driver = Driver::MYSQL;
}
