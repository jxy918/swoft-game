<?php declare(strict_types=1);


namespace Swoft\Process\Contract;


use Swoole\Process\Pool;

/**
 * Class WorkerStopInterface
 *
 * @since 2.0
 */
interface WorkerStopInterface
{
    /**
     * @param Pool $pool
     * @param int  $workerId
     */
    public function onWorkerStop(Pool $pool, int $workerId): void;
}