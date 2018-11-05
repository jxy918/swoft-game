<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Services;

use App\Lib\NotifyInterface;
use Swoft\Rpc\Server\Bean\Annotation\Service;
use Swoft\Core\ResultInterface;

use Swoft\App;
use App\Game\Core\Packet;
use App\Game\Conf\MainCmd;
use App\Game\Conf\SubCmd;
use App\Game\Core\Log;

/**
 * Notify servcie
 *
 * @method ResultInterface deferNotify(string $msg = '')
 *
 * @Service(version="1.0.0")
 */
class NotifyService implements NotifyInterface
{
    public function notify(string $msg = '')
    {
        $msg = 'this is system msg';
        $data = Packet::packFormat('OK', 0, $msg);
        $data = Packet::packEncode($data, MainCmd::CMD_SYS, SubCmd::BROADCAST_MSG_RESP);
        $ws_serv = App::$server->getServer();
        $conn_client = $this->pushToAll($ws_serv, $data);
        return [$conn_client];
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
                    $serv->push($fd, $data, WEBSOCKET_OPCODE_BINARY);
                }
            }
        }
        return $client;
    }
}