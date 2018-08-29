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
use Swoft\Db\Driver\Driver;
use Swoft\Db\Pool\Config\DbPoolProperties;

/**
 * OtherDbConfig
 *
 * @Bean()
 */
class OtherDbConfig extends DbPoolProperties
{
    /**
     * @Value(name="${config.db.other.master.name}", env="${DB_OTHER_NAME}")
     * @var string
     */
    protected $name = '';

    /**
     * @Value(name="${config.db.other.master.minActive}", env="${DB_OTHER_MIN_ACTIVE}")
     * @var int
     */
    protected $minActive = 5;

    /**
     * @Value(name="${config.db.other.master.maxActive}", env="${DB_OTHER_MAX_ACTIVE}")
     * @var int
     */
    protected $maxActive = 10;

    /**
     * @Value(name="${config.db.other.master.maxWait}", env="${DB_OTHER_MAX_WAIT}")
     * @var int
     */
    protected $maxWait = 20;

    /**
     * @Value(name="${config.db.other.master.maxIdleTime}", env="${DB_OTHER_MAX_IDLE_TIME}")
     * @var int
     */
    protected $maxIdleTime = 60;

    /**
     * @Value(name="${config.db.other.master.maxWaitTime}", env="${DB_OTHER_MAX_WAIT_TIME}")
     * @var int
     */
    protected $maxWaitTime = 3;

    /**
     * @Value(name="${config.db.other.master.timeout}", env="${DB_OTHER_TIMEOUT}")
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
     * @Value(name="${config.db.other.master.uri}", env="${DB_OTHER_URI}")
     * @var array
     */
    protected $uri = [];

    /**
     * the default driver is consul mysql
     *
     * @Value(name="${config.db.other.master.driver}", env="${DB_OTHER_DRIVER}")
     * @var string
     */
    protected $driver = Driver::MYSQL;
}
