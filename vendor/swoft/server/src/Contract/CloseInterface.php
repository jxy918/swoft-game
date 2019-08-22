<?php declare(strict_types=1);


namespace Swoft\Server\Contract;


use Swoole\Server;

/**
 * Class CloseInterface
 *
 * @since 2.0
 */
interface CloseInterface
{
    /**
     * Close event
     *
     * on connection closed
     * - you can do something. eg. record log
     *
     * @param Server $server
     * @param int      $fd
     * @param int      $reactorId
     */
    public function onClose(Server $server, int $fd, int $reactorId): void;
}