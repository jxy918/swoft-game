<?php

namespace Swoft\WebSocket\Server;

use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Server\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Interface HandlerInterface
 * @package Swoft\WebSocket\Server\Controller
 */
interface HandlerInterface
{
    const HANDSHAKE_OK = 0;
    const HANDSHAKE_FAIL = 1;

    /**
     * 在这里你可以验证握手的请求信息
     * - 必须返回含有两个元素的array
     *  - 第一个元素的值来决定是否进行握手
     *  - 第二个元素是response对象
     * - 可以在response设置一些自定义header,body等信息
     * @param Request $request
     * @param Response $response
     * @return array
     * [
     *  self::HANDSHAKE_OK,
     *  $response
     * ]
     */
    public function checkHandshake(Request $request, Response $response): array ;

    /**
     * @param Server $server
     * @param Request $request
     * @param int $fd
     */
    public function onOpen(Server $server, Request $request, int $fd);

    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame);

    /**
     * on connection closed
     * - you can do something. eg. record log
     * @param Server $server
     * @param int $fd
     * @return mixed
     */
    public function onClose(Server $server, int $fd);
}
