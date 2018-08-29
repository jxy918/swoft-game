<?php

namespace Swoft\Rpc\Server\Event\Listeners;

use Swoft\App;
use Swoft\Core\RequestContext;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventInterface;
use Swoft\Event\EventHandlerInterface;
use Swoft\Rpc\Server\Event\RpcServerEvent;

/**
 * Event after RPC request
 * @Listener(RpcServerEvent::AFTER_RECEIVE)
 */
class AfterReceiveListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        App::getLogger()->appendNoticeLog();
        RequestContext::destroy();
    }
}
