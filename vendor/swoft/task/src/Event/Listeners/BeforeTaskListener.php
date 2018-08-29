<?php

namespace Swoft\Task\Event\Listeners;

use Swoft\App;
use Swoft\Core\RequestContext;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventInterface;
use Swoft\Event\EventHandlerInterface;
use Swoft\Task\Event\TaskEvent;

/**
 * 任务前置事件
 *
 * @Listener(TaskEvent::BEFORE_TASK)
 * @uses      BeforeTaskListener
 * @version   2017年09月26日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BeforeTaskListener implements EventHandlerInterface
{
    /**
     * 事件回调
     *
     * @param EventInterface $event      事件对象
     */
    public function handle(EventInterface $event)
    {
        /* @var \Swoft\Task\Event\Events\BeforeTaskEvent $beforeEvent*/
        $beforeEvent = $event;

        $logid = $beforeEvent->getLogid();
        $spanid = $beforeEvent->getSpanid();
        $method = $beforeEvent->getMethod();
        $taskClass = $beforeEvent->getTaskClass();
        $uri = sprintf('%s->%s', $taskClass, $method);

        $contextData = [
            'logid'       => $logid,
            'spanid'      => $spanid,
            'uri'         => $uri,
            'requestTime' => microtime(true)
        ];
        RequestContext::setContextData($contextData);

        // 日志初始化
        App::getLogger()->initialize();

        // 连接池初始化
    }
}
