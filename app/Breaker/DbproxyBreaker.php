<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Breaker;

use Swoft\Sg\Bean\Annotation\Breaker;
use Swoft\Bean\Annotation\Value;
use Swoft\Sg\Circuit\CircuitBreaker;

/**
 * the breaker of dbproxy
 *
 * @Breaker("dbproxy")
 */
class DbproxyBreaker extends CircuitBreaker
{
    /**
     * The number of successive failures
     * If the arrival, the state switch to open
     *
     * @Value(name="${config.breaker.dbproxy.failCount}", env="${DBPROXY_BREAKER_FAIL_COUNT}")
     * @var int
     */
    protected $switchToFailCount = 3;

    /**
     * The number of successive successes
     * If the arrival, the state switch to close
     *
     * @Value(name="${config.breaker.dbproxy.successCount}", env="${DBPROXY_BREAKER_SUCCESS_COUNT}")
     * @var int
     */
    protected $switchToSuccessCount = 3;

    /**
     * Switch close to open delay time
     * The unit is milliseconds
     *
     * @Value(name="${config.breaker.dbproxy.delayTime}", env="${DBPROXY_BREAKER_DELAY_TIME}")
     * @var int
     */
    protected $delaySwitchTimer = 500;
}