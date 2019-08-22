<?php declare(strict_types=1);


namespace Swoft\Process\Contract;

use Swoft\Process\Process;

/**
 * Class UserProcessInterface
 *
 * @since 2.0
 */
interface UserProcessInterface
{
    /**
     * Run
     *
     * @param Process $process
     */
    public function run(Process $process): void;

    /**
     * @return bool
     */
    public function isStdinOut(): bool;

    /**
     * @return int
     */
    public function getPipeType(): int;

    /**
     * @return bool
     */
    public function isCoroutine(): bool;
}