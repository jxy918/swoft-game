<?php declare(strict_types=1);


namespace Swoft\Breaker\Contract;

/**
 * Class StateInterface
 *
 * @since 2.0
 */
interface StateInterface
{
    /**
     * Check status
     */
    public function check(): void;

    /**
     * Reset
     */
    public function reset(): void;

    /**
     * Success
     */
    public function success(): void;

    /**
     * Exception
     */
    public function exception(): void;
}