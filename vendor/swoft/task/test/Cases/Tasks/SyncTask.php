<?php

namespace SwoftTest\Task\Tasks;

use Swoft\Task\Bean\Annotation\Scheduled;
use Swoft\Task\Bean\Annotation\Task;

/**
 * Sync task
 *
 * @Task("sync")
 */
class SyncTask
{
    /**
     * crontab定时任务
     * 每一秒执行一次
     *
     * @Scheduled(cron="* * * * * *")
     */
    public function cronTask()
    {
        echo time() . "每一秒执行一次  \n";
        return 'cron';
    }

    /**
     * 每分钟第3-5秒执行
     *
     * @Scheduled(cron="3-5 * * * * *")
     */
    public function cronooTask()
    {
        echo time() . "第3-5秒执行\n";
        return 'cron';
    }

    /**
     * 每3秒执行
     *
     * @Scheduled(cron="*\/3 * * * * *")
     */
    public function testTask()
    {
        echo time() . "第3秒执行\n";
        return 'cron';
    }
}
