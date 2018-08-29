<?php

namespace Swoft\Rpc\Server\Event\Listeners;

use Swoft\Core\RequestContext;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\EventInterface;
use Swoft\Event\EventHandlerInterface;
use Swoft\Rpc\Server\Event\RpcServerEvent;

/**
 * Event before RPC request
 * @Listener(RpcServerEvent::BEFORE_RECEIVE)
 */
class BeforeReceiveListener implements EventHandlerInterface
{

    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        $params = $event->getParams();

        if (! isset($params[0])) {
            return;
        }

        $data = $params[0];
        $logid = $data['logid'] ?? uniqid('', true);
        $spanid = $data['spanid'] ?? 0;
        $uri = $data['func'] ?? 'null';

        $contextData = [
            'logid'       => $logid,
            'spanid'      => $spanid,
            'uri'         => $uri,
            'requestTime' => microtime(true),
        ];
        RequestContext::setContextData($contextData);
    }
}
