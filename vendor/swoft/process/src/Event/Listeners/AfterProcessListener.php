<?php

namespace Swoft\Process\Event\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventInterface;
use Swoft\Event\EventHandlerInterface;
use Swoft\Process\Event\ProcessEvent;

/**
 * After process listener
 *
 * @Listener(ProcessEvent::AFTER_PROCESS)
 */
class AfterProcessListener implements EventHandlerInterface
{
    /**
     * 事件回调
     *
     * @param EventInterface $event 事件对象
     * @return void
     */
    public function handle(EventInterface $event)
    {
        // 日志初始化
        App::getLogger()->appendNoticeLog(true);
    }
}
