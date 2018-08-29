<?php

namespace Swoft\Process\Event\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Core\RequestContext;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Process\Event\ProcessEvent;

/**
 * Before process listener
 *
 * @Listener(ProcessEvent::BEFORE_PROCESS)
 */
class BeforeProcessListener implements EventHandlerInterface
{
    /**
     * 事件回调
     *
     * @param EventInterface $event 事件对象
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $params = $event->getParams();

        if (count($params) < 1) {
            return;
        }

        // 初始化
        $spanid = 0;
        $logid = uniqid();

        $processName = $params[0];
        $uri = 'process-' . $processName;
        $flushInterval = 1;

        $contextData = [
            'logid'       => $logid,
            'spanid'      => $spanid,
            'uri'         => $uri,
            'requestTime' => microtime(true)
        ];

        App::getLogger()->setFlushInterval($flushInterval);
        RequestContext::setContextData($contextData);

        // 日志初始化
        App::getLogger()->initialize();
    }
}
