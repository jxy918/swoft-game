<?php

namespace Swoft\Bootstrap\Listeners\Interfaces;

use Swoole\Server;

/**
 * FinishInterface
 */
interface FinishInterface
{
    /**
     * @param Server $server
     * @param int    $taskId
     * @param mixed  $data
     *
     * @return mixed
     */
    public function onFinish(Server $server, int $taskId, $data);
}