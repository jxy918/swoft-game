<?php

namespace Swoft\Http\Server\Bootstrap\Listener;

use Swoft\App;
use Swoft\Bean\Annotation\ServerListener;
use Swoft\Bootstrap\Listeners\Interfaces\StartInterface;
use Swoft\Bootstrap\SwooleEvent;
use Swoole\Server;

/**
 * Class MasterStartListener
 * @package Swoft\Http\Server\Bootstrap\Listener
 * @ServerListener(event=SwooleEvent::ON_START)
 */
class MasterStartListener implements StartInterface
{
    public function onStart(Server $server)
    {
        \output()->writeln(
            'Server has been started. ' .
            "(master PID: <cyan>{$server->master_pid}</cyan>, manager PID: <cyan>{$server->manager_pid}</cyan>)"
        );

        // output a message before start
        if (!App::$server->isDaemonize()) {
            \output()->writeln("You can use <info>CTRL + C</info> to stop run.\n");
        }
    }
}
