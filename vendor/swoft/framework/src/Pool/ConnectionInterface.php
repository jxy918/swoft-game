<?php

namespace Swoft\Pool;

/**
 * Interface ConnectInterface
 *
 * @package Swoft\Pool
 */
interface ConnectionInterface
{
    /**
     * Create connectioin
     *
     * @return void
     */
    public function createConnection();

    /**
     * Reconnect
     */
    public function reconnect();

    /**
     * @return bool
     */
    public function check(): bool;

    /**
     * @return int
     */
    public function getLastTime(): int;

    /**
     * @return void
     */
    public function updateLastTime();

    /**
     * @return string
     */
    public function getConnectionId(): string;

    /**
     * @return \Swoft\Pool\PoolInterface
     */
    public function getPool(): \Swoft\Pool\PoolInterface;

    /**
     * @return bool
     */
    public function isAutoRelease(): bool;

    /**
     * @return bool
     */
    public function isRecv(): bool;

    /**
     * @param bool $autoRelease
     */
    public function setAutoRelease(bool $autoRelease);

    /**
     * @param bool $recv
     */
    public function setRecv(bool $recv);

    /**
     * @return mixed
     */
    public function receive();

    /**
     * @param bool $defer
     */
    public function setDefer($defer = true);

    /**
     * @return void
     */
    public function release($release = false);

}
