<?php

namespace Swoft\Http\Server\Event\Listeners;

use Swoft\App;
use Swoft\Core\RequestContext;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventInterface;
use Swoft\Event\EventHandlerInterface;
use Swoft\Http\Server\Event\HttpServerEvent;

/**
 * 请求后事件
 *
 * @Listener(HttpServerEvent::AFTER_REQUEST)
 */
class AfterRequestListener implements EventHandlerInterface
{
    /**
     * 事件回调
     *
     * @param EventInterface $event      事件对象
     */
    public function handle(EventInterface $event)
    {
        App::getLogger()->appendNoticeLog();
        RequestContext::destroy();
    }
}
