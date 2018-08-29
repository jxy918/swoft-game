<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Sg\Circuit;

use Swoft\App;
use Swoole\Lock;

/**
 * 熔断器
 */
class CircuitBreaker
{
    /**
     * 关闭状态
     */
    const CLOSE = 'close';

    /**
     * 开启状态
     */
    const OPEN = 'open';

    /**
     * 半开起状态
     */
    const HALF_OPEN_STATE = 'halfOpenState';

    /**
     * 未初始化
     */
    const PENDING = 'pending';

    /**
     * @var int 错误请求计数
     */
    public $failCounter = 0;

    /**
     * @var int 成功请求计数
     */
    public $successCounter = 0;

    /**
     * @var int 开启状态切换到半开状态时间
     */
    public $switchOpenToHalfOpenTime = 0;

    /**
     * @var string 服务名称
     */
    public $serviceName = 'breakerService';

    /**
     * @var CircuitBreakerState 熔断器状态，开启、半开、关闭
     */
    private $circuitState = null;

    /**
     * @var \Swoole\Lock 半开状态锁
     */
    protected $halfOpenLock = null;

    /**
     * The number of successive failures
     * If the arrival, the state switch to open
     *
     * @var int
     */
    protected $switchToFailCount = 6;

    /**
     * The number of successive successes
     * If the arrival, the state switch to close
     *
     * @var int
     */
    protected $switchToSuccessCount = 6;

    /**
     * Switch close to open delay time
     * The unit is milliseconds
     *
     * @var int
     */
    protected $delaySwitchTimer = 5000;

    /**
     * 初始化
     */
    public function init()
    {
        // 状态初始化
        $this->circuitState = new CloseState($this);
        $this->halfOpenLock = new Lock(SWOOLE_MUTEX);
    }

    /**
     * 熔断器调用
     *
     * @param mixed $callback 回调函数
     * @param array $params   参数
     * @param mixed $fallback 失败回调
     *
     * @return mixed 返回结果
     */
    public function call($callback, $params = [], $fallback = null)
    {
        return $this->circuitState->doCall($callback, $params, $fallback);
    }

    /**
     * 失败计数
     */
    public function incFailCount()
    {
        $this->failCounter++;
    }

    /**
     * 成功计数
     */
    public function incSuccessCount()
    {
        $this->successCounter++;
    }

    /**
     * 是否是关闭状态
     *
     * @return bool
     */
    public function isClose()
    {
        return $this->circuitState instanceof CloseState;
    }

    /**
     * 是否是开启状态
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->circuitState instanceof OpenState;
    }

    /**
     * 是否是半开状态
     *
     * @return bool
     */
    public function isHalfOpen()
    {
        return $this->circuitState instanceof HalfOpenState;
    }

    /**
     * 切换到关闭
     */
    public function switchToCloseState()
    {
        App::debug($this->serviceName . '服务，当前[' . $this->getCurrentState() . ']，熔断器状态切换，切换到[关闭]状态');
        $this->circuitState = new CloseState($this);
    }

    /**
     * 切换到开启
     */
    public function switchToOpenState()
    {
        App::debug($this->serviceName . '服务，当前[' . $this->getCurrentState() . ']，熔断器状态切换，切换到[开启]状态');
        $this->circuitState = new OpenState($this);
    }

    /**
     * 切换到半开
     */
    public function switchToHalfState()
    {
        App::debug($this->serviceName . '服务，当前[' . $this->getCurrentState() . ']，熔断器状态切换，切换到[半开]状态');

        $this->circuitState = new HalfOpenState($this);
    }

    /**
     * 降级处理
     *
     * @param mixed $fallback
     * @param array $params
     *
     * @return null
     */
    public function fallback($fallback = null, array $params = [])
    {
        if ($fallback == null) {
            App::debug($this->serviceName . '服务，当前[' . $this->getCurrentState() . ']，服务降级处理，fallback未定义');
            return null;
        }

        if (is_array($fallback) && count($fallback) == 2) {
            list($fallbackObj, $method) = $fallback;
            App::debug($this->serviceName . '服务，服务降级处理，执行fallback, class=' . get_class($fallbackObj) . ' method=' . $method);

            return $fallbackObj->$method(...$params);
        }

        return null;
    }

    /**
     * 当前状态
     *
     * @return string
     */
    public function getCurrentState()
    {
        if ($this->circuitState instanceof CloseState) {
            return self::CLOSE;
        }
        if ($this->circuitState instanceof HalfOpenState) {
            return self::HALF_OPEN_STATE;
        }

        if ($this->circuitState instanceof OpenState) {
            return self::OPEN;
        }
        return self::PENDING;
    }

    /**
     * 初始化计数
     */
    public function initCounter()
    {
        $this->failCounter = 0;
        $this->successCounter = 0;
    }

    /**
     * 获取失败计数
     *
     * @return int
     */
    public function getFailCounter(): int
    {
        return $this->failCounter;
    }

    /**
     * 获取成功计数
     *
     * @return int
     */
    public function getSuccessCounter(): int
    {
        return $this->successCounter;
    }

    /**
     * 获取开始切换到失败的计数
     *
     * @return int
     */
    public function getSwitchToFailCount(): int
    {
        return $this->switchToFailCount;
    }

    /**
     * 开始切换到成功的计数
     *
     * @return int
     */
    public function getSwitchToSuccessCount(): int
    {
        return $this->switchToSuccessCount;
    }

    /**
     * 获取开启切换到半开的时间
     *
     * @return int
     */
    public function getSwitchOpenToHalfOpenTime(): int
    {
        return $this->switchOpenToHalfOpenTime;
    }

    /**
     * 初始化开启切换到半开的时间
     *
     * @param int $switchOpenToHalfOpenTime
     */
    public function setSwitchOpenToHalfOpenTime(int $switchOpenToHalfOpenTime)
    {
        $this->switchOpenToHalfOpenTime = $switchOpenToHalfOpenTime;
    }

    /**
     * 关闭切换到开启延迟定时器时间
     *
     * @return int
     */
    public function getDelaySwitchTimer(): int
    {
        return $this->delaySwitchTimer;
    }

    /**
     * 半开状态锁
     *
     * @return \Swoole\Lock
     */
    public function getHalfOpenLock()
    {
        return $this->halfOpenLock;
    }
}
