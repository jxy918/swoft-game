<?php

namespace Swoft\Process\Bootstrap\Process;

use Swoft\App;
use Swoft\Process\Bean\Annotation\Process;
use Swoft\Process\Bootstrap\Reload;
use Swoft\Process\Process as SwoftProcess;
use Swoft\Process\ProcessInterface;


/**
 * Relaod process
 *
 * @Process(name="reload", boot=true)
 */
class ReloadProcess implements ProcessInterface
{
    /**
     * @param \Swoft\Process\Process $process
     */
    public function run(SwoftProcess $process)
    {
        $pname = App::$server->getPname();
        $processName = sprintf('%s reload process', $pname);
        $process->name($processName);

        /* @var \Swoft\Process\Bootstrap\Reload $relaod */
        $relaod = App::getBean(Reload::class);
        $relaod->run();
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        if (! App::getAppProperties()->get('server.server.autoReload', false)) {
            return false;
        }
        return true;
    }
}
