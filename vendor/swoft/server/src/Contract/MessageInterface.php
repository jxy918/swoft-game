<?php declare(strict_types=1);


namespace Swoft\Server\Contract;


use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Class MessageInterface
 *
 * @since 2.0
 */
interface MessageInterface
{
    /**
     * Message event
     *
     * @param Server $server
     * @param Frame  $frame
     */
    public function onMessage(Server $server, Frame $frame): void;
}