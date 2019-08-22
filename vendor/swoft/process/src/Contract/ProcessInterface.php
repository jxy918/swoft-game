<?php declare(strict_types=1);


namespace Swoft\Process\Contract;

use Swoole\Process\Pool;

/**
 * Class ProcessInterface
 *
 * @since 2.0
 */
interface ProcessInterface
{
    /**
     * Run
     *
     * @param Pool $pool
     * @param int  $workerId
     */
    public function run(Pool $pool, int $workerId): void;
}