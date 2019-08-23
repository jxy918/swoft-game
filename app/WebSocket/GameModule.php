<?php declare(strict_types=1);

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

use App\WebSocket\Game\Core\Packet;
use App\WebSocket\Game\Core\Dispatch;
use App\WebSocket\Game\Core\Log;
use App\WebSocket\Game\Conf\MainCmd;
use App\WebSocket\Game\Conf\SubCmd;
use function server;
use Swoft\Redis\Redis;

/**
 * Class GameModule
 *
 * @WsModule(
 *     "/game"
 * )
 */
class GameModule
{
    /**
     * 用户信息redis的key
     */
    const USER_INFO_KEY = 'user:info:%s';

    /**
     * 设置key过期时间， 设置为7天
     */
    const EXPIRE = 7 * 24 * 60 * 60;

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
        $cookie = $request->getCookieParams();
        if(isset($cookie['USER_INFO'])) {
            $uinfo = json_decode($cookie['USER_INFO'], true);
            //允许连接， 并记录用户信息
            $uinfo['fd'] = $fd;
            $user_info_key = sprintf(self::USER_INFO_KEY, $uinfo['account']);
            $data = Redis::get($user_info_key);
            //之前信息存在， 清除之前的连接
            if(!empty($data)) {
                $data = json_decode($data, true);
                if(isset($data['fd'])) {
                    //处理双开的情况
                    $this->loginFail($data['fd'], '1');
                    server()->disconnect($data['fd']);
                    //清理redis
                    Redis::del($user_info_key);
                }
            }
            //保存登陆信息
            $this->user_info[$uinfo['account']] = $uinfo;
            Redis::set($user_info_key, json_encode($uinfo), self::EXPIRE);
        } else {
            $this->loginFail($fd, '2');
        }
    }

    /**
     * @OnMessage()
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame)
    {
        Log::show(" Message: client #{$frame->fd} push success Mete: \n{");
        $data = Packet::packDecode($frame->data);
        if(isset($data['code']) && $data['code'] == 0 && isset($data['msg']) && $data['msg'] == 'OK') {
            Log::show('Recv <<<  cmd='.$data['cmd'].'  scmd='.$data['scmd'].'  len='.$data['len'].'  data='.json_encode($data['data']));
            //转发请求，代理模式处理,websocket路由到相关逻辑
            $data['serv'] = $server;
            $obj = new Dispatch($data);
            $back = "<center><h1>404 Not Found </h1></center><hr><center>swoft</center>\n";
            if(!empty($obj->getStrategy())) {
                $back = $obj->exec();
                if($back) {
                    $server->push($frame->fd, $back, WEBSOCKET_OPCODE_BINARY);
                }
            }
            Log::show('Tcp Strategy <<<  data='.$back);
        } else {
            Log::show($data['msg']);
        }
        Log::split('}');
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
        $this->loginFail($fd, '3');
    }

    /**
     * 发送登陆失败请求到客户端
     * @param $fd
     * @param string $msg
     */
    private function loginFail($fd, $msg = '')
    {
        //原封不动发回去
        $server = server();
        if($server->getClientInfo($fd) !== false) {
            $data = Packet::packFormat('OK', 0, array('data' => 'login fail'.$msg));
            $back = Packet::packEncode($data, MainCmd::CMD_SYS, SubCmd::LOGIN_FAIL_RESP);
            $server->push($fd, $back, WEBSOCKET_OPCODE_BINARY);
        }
    }
}
