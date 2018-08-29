<?php

namespace Swoft\Task\Bootstrap\Process;

use Swoft\App;
use Swoft\Process\Bean\Annotation\Process;
use Swoft\Process\Process as SwoftProcess;
use Swoft\Process\ProcessInterface;
use Swoft\Task\Task;

/**
 * Crontab process
 *
 * @Process(name="cronExec", boot=true)
 */
class CronExecProcess implements ProcessInterface
{
    /**
     * @param \Swoft\Process\Process $process
     */
    public function run(SwoftProcess $process)
    {
        $pname = App::$server->getPname();
        $process->name(sprintf('%s cronexec process', $pname));

        /** @var \Swoft\Task\Crontab\Crontab $cron */
        $cron = App::getBean('crontab');

        // Swoole/HttpServer
        $server = App::$server->getServer();

        $server->tick(0.5 * 1000, function () use ($cron) {
            $tasks = $cron->getExecTasks();
            if (!empty($tasks)) {
                foreach ($tasks as $task) {
                    // Diliver task
                    Task::deliverByProcess($task['taskClass'], $task['taskMethod']);
                    $cron->finishTask($task['key']);
                }
            }
        });
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        $serverSetting = App::$server->getServerSetting();
        $cronable = (int)$serverSetting['cronable'];
        if ($cronable !== 1) {
            return false;
        }

        return true;
    }
}
