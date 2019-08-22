<?php declare(strict_types=1);


namespace Swoft\Process;

use Swoft\Process\Contract\WorkerStartInterface;
use Swoft\Process\Contract\WorkerStopInterface;

/**
 * Class SwooleEvent
 *
 * @since 2.0
 */
class SwooleEvent
{
    /**
     * Worker start
     */
    public const WORKER_START = 'workerStart';

    /**
     * Worker stop
     */
    public const WORKER_STOP = 'workerStop';

    /**
     * Listener mapping
     */
    public const LISTENER_MAPPING = [
        self::WORKER_START => WorkerStartInterface::class,
        self::WORKER_STOP  => WorkerStopInterface::class
    ];
}