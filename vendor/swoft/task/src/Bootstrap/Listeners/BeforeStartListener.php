<?php

namespace Swoft\Task\Bootstrap\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bootstrap\Listeners\Interfaces\BeforeStartInterface;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Task\Crontab\TableCrontab;

/**
 * The listener of before start
 * @BeforeStart()
 */
class BeforeStartListener implements BeforeStartInterface
{
    /**
     * @param AbstractServer $server
     */
    public function onBeforeStart(AbstractServer $server)
    {
        /** @var array[] $settings */
        $settings = App::getAppProperties()->get('server');
        $settings = $settings['server'];

        // Init crontab share memory table
        if (isset($settings['cronable']) && (int)$settings['cronable'] === 1) {
            $this->initCrontabMemoryTable();
        }
    }

    /**
     * init table of crontab
     */
    private function initCrontabMemoryTable()
    {
        /** @var array[] $settings */
        $settings = App::getAppProperties()->get('server');
        $settings = $settings['crontab'];

        $taskCount = isset($settings['task_count']) && $settings['task_count'] > 0 ? $settings['task_count'] : null;
        $taskQueue = isset($settings['task_queue']) && $settings['task_queue'] > 0 ? $settings['task_queue'] : null;

        TableCrontab::init($taskCount, $taskQueue);
    }
}
