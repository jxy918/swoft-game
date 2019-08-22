<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Contract;

use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoole\WebSocket\Server;

/**
 * Interface WsModuleInterface
 *
 * @since 2.0
 */
interface WsModuleInterface
{
    /**
     * Here you can verify the request information for the handshake
     * - You must return an array with two elements
     *  - The value of the first element to decide whether to handshake
     *  - The second element is the response object
     * - You can set some custom headers and other information in the response.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return array [bool, $response]
     */
    public function checkHandshake(Request $request, Response $response): array;

    /**
     * On connection has open
     *
     * @param Server  $server
     * @param Request $request
     * @param int     $fd
     */
    public function onOpen(Server $server, Request $request, int $fd): void;

    /**
     * On connection closed
     * - you can do something. eg. record log
     *
     * @param Server $server
     * @param int    $fd
     */
    public function onClose(Server $server, int $fd): void;
}
