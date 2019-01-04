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

use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Server\Response;
use Swoft\WebSocket\Server\Bean\Annotation\WebSocket;
use Swoft\WebSocket\Server\HandlerInterface;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use App\Middlewares\GameMiddleware;

/**
 * Class VedioController - This is an controller for handle websocket
 * @package App\WebSocket
 * @WebSocket("/vedio")
 */
class VedioController implements HandlerInterface
{
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
    public function checkHandshake(Request $request, Response $response): array
    {
        // some validate logic ...
        return [self::HANDSHAKE_OK, $response];
    }

    /**
     *
     * @param Server $server
     * @param Request $request
     * @param int $fd
     * @return mixed
     */
    public function onOpen(Server $server, Request $request, int $fd)
    {
        echo "vedio connnected #{$fd}...";

    }

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
     */
    public function onMessage(Server $server, Frame $frame)
    {
        //广播消息
        echo "vedio message #{$frame->fd}...data:".$frame->data."\n";
        if(!is_numeric($frame->data)) {
            //如果是录像数据， 发送二进制数据
            $this->pushToAll($server, $frame->data, WEBSOCKET_OPCODE_BINARY);
        }
    }

    /**
     * @param Server $server
     * @param int $fd
     * @return mixed
     */
    public function onRequest(Request $request, Response $response)
    {
        echo 'vedio request ...';
    }

    /**
     * @param Server $server
     * @param int $fd
     * @return mixed
     */
    public function onClose(Server $server, int $fd)
    {
        echo "vedio close #{$fd}...";
    }

    /**
     * 当connetions属性无效时可以使用此方法，服务器广播消息， 此方法是给所有的连接客户端， 广播消息，通过方法getClientList广播
     * @param $serv
     * @param $data
     */
    protected function pushToAll($serv, $data)
    {
        $client = array();
        $start_fd = 0;
        while(true) {
            $conn_list = $serv->getClientList($start_fd, 10);
            if ($conn_list===false or count($conn_list) === 0) {
                Log::show('BroadCast finish');
                break;
            }
            $start_fd = end($conn_list);
            foreach($conn_list as $fd) {
                //获取客户端信息
                $client_info = $serv->getClientInfo($fd);
                $client[$fd] = $client_info;
                if(isset($client_info['websocket_status']) && $client_info['websocket_status'] == 3) {
                    $serv->push($fd, $data);
                }
            }
        }
        //return $client;
    }
}
