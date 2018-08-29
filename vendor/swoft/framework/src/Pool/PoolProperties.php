<?php

namespace Swoft\Pool;

/**
 * Class PoolProperties Pool properties
 * @package Swoft\Pool
 */
class PoolProperties implements PoolConfigInterface
{
    /**
     * Pool name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Minimum active number of connections
     *
     * @var int
     */
    protected $minActive = 5;

    /**
     * Maximum active number of connections
     *
     * @var int
     */
    protected $maxActive = 10;

    /**
     * Maximum waiting for the number of connections, if there is no limit to 0
     *
     * @var int
     */
    protected $maxWait = 20;

    /**
     * Maximum waiting time
     *
     * @var int
     */
    protected $maxWaitTime = 3;

    /**
     * Maximum idle time
     *
     * @var int
     */
    protected $maxIdleTime = 60;

    /**
     * Connection timeout
     *
     * @var int
     */
    protected $timeout = 3;

    /**
     * Connection addresses
     * <pre>
     * [
     *  '127.0.0.1:88',
     *  '127.0.0.1:88'
     * ]
     * </pre>
     *
     * @var array
     */
    protected $uri = [];

    /**
     * Whether to user provider(consul/etcd/zookeeper)
     *
     * @var bool
     */
    protected $useProvider = false;

    /**
     * Default balancer
     *
     * @var string
     */
    protected $balancer = 'random';

    /**
     * Default provider
     *
     * @var string
     */
    protected $provider = 'consul';

    /**
     * Initialize
     */
    public function init()
    {
        if (empty($this->name)) {
            $this->name = uniqid();
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMaxActive(): int
    {
        return $this->maxActive;
    }

    /**
     * @return int
     */
    public function getMaxWait(): int
    {
        return $this->maxWait;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return array
     */
    public function getUri(): array
    {
        return $this->uri;
    }

    /**
     * @return bool
     */
    public function isUseProvider(): bool
    {
        return $this->useProvider;
    }

    /**
     * @return string
     */
    public function getBalancer(): string
    {
        return $this->balancer;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @return int
     */
    public function getMinActive(): int
    {
        return $this->minActive;
    }

    /**
     * @return int
     */
    public function getMaxWaitTime(): int
    {
        return $this->maxWaitTime;
    }

    /**
     * @return int
     */
    public function getMaxIdleTime(): int
    {
        return $this->maxIdleTime;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
