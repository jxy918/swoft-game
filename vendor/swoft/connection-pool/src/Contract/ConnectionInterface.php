<?php declare(strict_types=1);


namespace Swoft\Connection\Pool\Contract;


/**
 * Class ConnectionInterface
 *
 * @since 2.0
 */
interface ConnectionInterface
{
    /**
     * Create connection
     */
    public function create(): void;

    /**
     * Reconnect connection
     */
    public function reconnect(): bool;

    /**
     * Get connection id
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Release connection
     *
     * @param bool $force
     */
    public function release(bool $force = false): void;

    /**
     * Get last time
     *
     * @return int
     */
    public function getLastTime(): int;

    /**
     * Update last time
     */
    public function updateLastTime(): void;

    /**
     * Set whether to release
     *
     * @param bool $release
     */
    public function setRelease(bool $release): void;

    /**
     * @param string $poolName
     */
    public function setPoolName(string $poolName): void;

    /**
     * Close connection
     */
    public function close(): void;
}