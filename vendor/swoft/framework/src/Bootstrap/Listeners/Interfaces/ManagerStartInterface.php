<?php

namespace Swoft\Bootstrap\Listeners\Interfaces;

use Swoole\Server;

/**
 * Interface ManagerStartInterface
 * @package Swoft\Bootstrap\Listeners\Interfaces
 */
interface ManagerStartInterface
{
    public function onManagerStart(Server $server);
}
