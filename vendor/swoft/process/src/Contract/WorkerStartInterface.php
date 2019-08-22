<?php declare(strict_types=1);


namespace Swoft\Process\Contract;

use Swoole\Process\Pool;

/**
 * Class WorkerStartInterface
 *
 * @since 2.0
 */
interface WorkerStartInterface
{
    /**
     * @param Pool $pool
     * @param int  $workerId
     */
    public function onWorkerStart(Pool $pool, int $workerId): void;
}