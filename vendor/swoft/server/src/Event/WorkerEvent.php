<?php declare(strict_types=1);

namespace Swoft\Server\Event;

use Swoole\Server;

/**
 * Class WorkerEvent
 *
 * @since 2.0
 */
class WorkerEvent extends ServerStartEvent
{
    /**
     * @var int
     */
    public $workerId = 0;

    /**
     * @var int
     */
    public $workerPid;

    /**
     * @var bool
     */
    public $taskProcess = false;

    /**
     * Class constructor.
     *
     * @param string $name
     * @param Server $server
     * @param int    $workerId
     */
    public function __construct(string $name, Server $server, int $workerId)
    {
        parent::__construct($name, $server);

        $this->workerId  = $workerId;
        $this->workerPid = $server->worker_pid;
    }
}
