<?php

namespace Swoft\WebSocket\Server\Event;

/**
 * Class WsEvent
 * @package Swoft\WebSocket\Server\Event
 */
final class WsEvent
{
    const ON_HANDSHAKE = 'ws.handshake';
    const ON_OPEN = 'ws.open';
    const ON_MESSAGE = 'ws.message';
    const ON_CLOSE = 'ws.close';
    const ON_ERROR = 'ws.error';
}
