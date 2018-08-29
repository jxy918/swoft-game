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
 * Master pool
 *
 * @Bean()
 */
class DbPoolConfig extends DbPoolProperties
{
    /**
     * @Value(name="${config.db.master.name}", env="${DB_NAME}")
     * @var string
     */
    protected $name = '';

    /**
     * @Value(name="${config.db.master.minActive}", env="${DB_MIN_ACTIVE}")
     * @var int
     */
    protected $minActive = 5;

    /**
     * @Value(name="${config.db.master.maxActive}", env="${DB_MAX_ACTIVE}")
     * @var int
     */
    protected $maxActive = 10;

    /**
     * @Value(name="${config.db.master.maxWait}", env="${DB_MAX_WAIT}")
     * @var int
     */
    protected $maxWait = 20;

    /**
     * @Value(name="${config.db.master.maxIdleTime}", env="${DB_MAX_IDLE_TIME}")
     * @var int
     */
    protected $maxIdleTime = 60;

    /**
     * @Value(name="${config.db.master.maxWaitTime}", env="${DB_MAX_WAIT_TIME}")
     * @var int
     */
    protected $maxWaitTime = 3;

    /**
     * @Value(name="${config.db.master.timeout}", env="${DB_TIMEOUT}")
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
     * @Value(name="${config.db.master.uri}", env="${DB_URI}")
     * @var array
     */
    protected $uri = [];

    /**
     * whether to user provider(consul/etcd/zookeeper)
     *
     * @Value(name="${config.db.master.useProvider}", env="${DB_USE_PROVIDER}")
     * @var bool
     */
    protected $useProvider = false;

    /**
     * the default balancer is random balancer
     *
     * @Value(name="${config.db.master.balancer}", env="${DB_BALANCER}")
     * @var string
     */
    protected $balancer = '';

    /**
     * the default provider is consul provider
     *
     * @Value(name="${config.db.master.provider}", env="${DB_PROVIDER}")
     * @var string
     */
    protected $provider = '';

    /**
     * the default driver is consul mysql
     *
     * @Value(name="${config.db.master.driver}", env="${DB_DRIVER}")
     * @var string
     */
    protected $driver = Driver::MYSQL;
}
