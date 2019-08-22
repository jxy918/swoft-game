<?php declare(strict_types=1);


namespace Swoft\Contract;

/**
 * Class WaitGroupInterface
 *
 * @since 2.0
 */
interface WaitGroupInterface
{
    /**
     * Add task
     */
    public function add(): void;

    /**
     * Done task
     */
    public function done(): void;

    /**
     * Wait task
     */
    public function wait(): void;
}