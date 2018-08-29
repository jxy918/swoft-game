<?php

namespace Swoft\Bootstrap\Listeners\Interfaces;

use Swoole\Server;

/**
 * Interface WorkerStartInterface
 * @package Swoft\Bootstrap\Listeners\Interfaces
 */
interface WorkerStartInterface
{
    public function onWorkerStart(Server $server, int $workerId, bool $isWorker);
}
