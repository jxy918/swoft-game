<?php

namespace Swoft\Pool;

/**
 * Class AbstractConnect
 */
abstract class AbstractConnection implements ConnectionInterface
{
    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var int
     */
    protected $lastTime;

    /**
     * @var string
     */
    protected $connectionId;

    /**
     * @var bool
     */
    protected $autoRelease = true;

    /**
     * Whether or not the package has been recv,default true
     *
     * @var bool
     */
    protected $recv = true;

    /**
     * AbstractConnection constructor.
     *
     * @param PoolInterface $connectPool
     */
    public function __construct(PoolInterface $connectPool)
    {
        $this->lastTime     = time();
        $this->connectionId = uniqid();
        $this->pool         = $connectPool;
        $this->createConnection();
    }

    /**
     * @return int
     */
    public function getLastTime(): int
    {
        return $this->lastTime;
    }

    /**
     * Update last time
     */
    public function updateLastTime()
    {
        $this->lastTime = time();
    }

    /**
     * @return string
     */
    public function getConnectionId(): string
    {
        return $this->connectionId;
    }

    /**
     * @return \Swoft\Pool\PoolInterface
     */
    public function getPool(): \Swoft\Pool\PoolInterface
    {
        return $this->pool;
    }

    /**
     * @return bool
     */
    public function isAutoRelease(): bool
    {
        return $this->autoRelease;
    }

    /**
     * @return bool
     */
    public function isRecv(): bool
    {
        return $this->recv;
    }

    /**
     * @param bool $autoRelease
     */
    public function setAutoRelease(bool $autoRelease)
    {
        $this->autoRelease = $autoRelease;
    }

    /**
     * @param bool $recv
     */
    public function setRecv(bool $recv)
    {
        $this->recv = $recv;
    }

    /**
     * @param bool $release
     */
    public function release($release = false)
    {
        if ($this->isAutoRelease() || $release) {
            $this->pool->release($this);
        }
    }

    public function receive()
    {

    }

    public function setDefer($defer = true)
    {

    }
}
