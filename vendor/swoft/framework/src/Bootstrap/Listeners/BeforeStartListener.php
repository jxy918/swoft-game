<?php

namespace Swoft\Bootstrap\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bean\Collector\SwooleListenerCollector;
use Swoft\Bootstrap\Listeners\Interfaces\BeforeStartInterface;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Exception\InvalidArgumentException;
use Swoole\Lock;

/**
 * the listener of before server start
 *
 * @BeforeStart()
 */
class BeforeStartListener implements BeforeStartInterface
{
    /**
     * @param AbstractServer $server
     * @throws \Swoft\Exception\InvalidArgumentException
     */
    public function onBeforeStart(AbstractServer $server)
    {
        // init worker lock
        $server->setWorkerLock(new Lock(SWOOLE_RWLOCK));

        // check task
        $this->checkTask();
    }

    /**
     * check task
     * @throws \Swoft\Exception\InvalidArgumentException
     */
    private function checkTask()
    {
        $settings  = App::getAppProperties()->get('server');
        $settings  = $settings['setting'];
        $collector = SwooleListenerCollector::getCollector();

        $isConfigTask  = isset($settings['task_worker_num']) && $settings['task_worker_num'] > 0;
        $isInstallTask = isset($collector[SwooleEvent::TYPE_SERVER][SwooleEvent::ON_TASK]) && isset($collector[SwooleEvent::TYPE_SERVER][SwooleEvent::ON_FINISH]);

        if ($isConfigTask && !$isInstallTask) {
            throw new InvalidArgumentException("Please 'composer require swoft/task' or set task_worker_num=0 !");
        }

        if (!$isConfigTask && $isInstallTask) {
            throw new InvalidArgumentException('Please set task_worker_num > 0 !');
        }
    }
}
