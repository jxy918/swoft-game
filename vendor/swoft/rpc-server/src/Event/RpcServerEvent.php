<?php

namespace Swoft\Rpc\Server\Event;

/**
 * RPC Server event defines
 */
class RpcServerEvent
{
    /**
     * Before rpc request
     */
    const BEFORE_RECEIVE = 'beforeReceive';

    /**
     * After rpc request
     */
    const AFTER_RECEIVE = 'afterReceive';
}