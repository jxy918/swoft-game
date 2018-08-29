<?php

namespace Swoft\Sg\Circuit;

/**
 * 熔断器状态close open half-open
 */
abstract class CircuitBreakerState
{
    /**
     * @var CircuitBreaker 熔断器
     */
    protected $circuitBreaker = null;

    /**
     * CircuitBreakerState constructor.
     *
     * @param CircuitBreaker $circuitBreaker 熔断器
     */
    public function __construct(CircuitBreaker $circuitBreaker)
    {
        $this->circuitBreaker = $circuitBreaker;
        $this->circuitBreaker->initCounter();
    }

    /**
     * 熔断器服务名称
     *
     * @return string
     */
    protected function getServiceName()
    {
        return $this->circuitBreaker->serviceName;
    }

    abstract public function doCall($callback, $params = [], $fallback = null);
}
