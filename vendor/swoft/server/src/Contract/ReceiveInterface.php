<?php declare(strict_types=1);


namespace Swoft\Server\Contract;


use Swoole\Server;

/**
 * Class ReceiveInterface
 *
 * @since 2.0
 */
interface ReceiveInterface
{
    /**
     * Receive event
     *
     * @param Server $server
     * @param int      $fd
     * @param int      $reactorId
     * @param string   $data
     */
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): void;
}