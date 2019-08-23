<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\WebSocket;

use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\WebSocket\Server\Annotation\Mapping\OnClose;
use Swoft\WebSocket\Server\Annotation\Mapping\OnHandshake;
use Swoft\WebSocket\Server\Annotation\Mapping\OnOpen;
use Swoft\WebSocket\Server\Annotation\Mapping\OnMessage;
use Swoft\WebSocket\Server\Annotation\Mapping\WsModule;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use function server;
use const WEBSOCKET_OPCODE_BINARY;


/**
 * Class VedioModule
 *
 * @WsModule(
 *     "/vedio"
 * )
 */
class VedioModule
{

    /**
     * 在这里你可以验证握手的请求信息
     * @OnHandshake()
     * @param Request $request
     * @param Response $response
     * @return array [bool, $response]
     */
    public function checkHandshake(Request $request, Response $response): array
    {
        return [true, $response];
    }

    /**
     * @OnOpen()
     * @param Request $request
     * @param int     $fd
     */
    public function onOpen(Request $request, int $fd): void
    {
        echo "vedio connnected #{$fd}...";

    }

    /**
     * @OnMessage()
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame)
    {
        //广播消息
        echo "vedio message #{$frame->fd}...data:".$frame->data."\n";
        if(!is_numeric($frame->data)) {
            //如果是录像数据， 发送二进制数据
            $ret = server()->sendToAll($frame->data,  0,  50);
            var_dump($ret);
        }
    }


    /**
     * On connection closed
     * - you can do something. eg. record log
     *
     * @OnClose()
     * @param Server $server
     * @param int    $fd
     */
    public function onClose(Server $server, int $fd): void
    {
        //清除登陆信息变量
        echo "vedio close #{$fd}...";
    }
}
