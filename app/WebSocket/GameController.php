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

use App\Game\Core\Packet;
use App\Game\Core\Dispatch;
use App\Game\Core\Log;
use App\Game\Conf\MainCmd;
use App\Game\Conf\SubCmd;

use App\Middlewares\GameMiddleware;

/**
 * Class GameController - This is an controller for handle websocket
 * @package App\WebSocket
 * @WebSocket("/game")
 */
class GameController implements HandlerInterface
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
        $cookie = $request->getCookieParams();
        if(isset($cookie['USER_INFO'])) {
            $uinfo = json_decode($cookie['USER_INFO'], true);
            //允许连接， 并记录用户信息
            $uinfo['fd'] = $fd;
            $user_info_key = sprintf(self::USER_INFO_KEY, $uinfo['account']);
            $data = cache()->get($user_info_key);
            $data = json_decode($data, true);
            //之前信息存在， 清除之前的连接
            if(!empty($data)) {
                if(isset($data['fd'])) {
                    //处理双开的情况
                    $this->loginFail($server, $data['fd'], '1');
                    $server->close($data['fd']);
                    //清理redis
                    cache()->delete($user_info_key);
                }
            }
            //保存登陆信息
            $this->user_info[$uinfo['account']] = $uinfo;
            cache()->set($user_info_key, json_encode($uinfo), self::EXPIRE);
        } else {
            $this->loginFail($server, $fd, '2');
        }
    }

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
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
     * 发送登陆失败请求到客户端
     * @param Server $server
     * @param $fd
     * @return \Psr\SimpleCache\CacheInterface|string
     */
    private function loginFail(Server $server, $fd, $msg = '')
    {
        //原封不动发回去
        if($server->getClientInfo($fd) !== false) {
            $data = Packet::packFormat('OK', 0, array('data' => 'login fail'.$msg));
            $back = Packet::packEncode($data, MainCmd::CMD_SYS, SubCmd::LOGIN_FAIL_RESP);
            $server->push($fd, $back, WEBSOCKET_OPCODE_BINARY);
        }
    }

    /**
     * @param Server $server
     * @param int $fd
     * @return mixed
     */
    public function onRequest(Request $request, Response $response)
    {
        var_dump($request['server']);
    }

    /**
     * @param Server $server
     * @param int $fd
     * @return mixed
     */
    public function onClose(Server $server, int $fd)
    {
        //清除登陆信息变量
        $this->loginFail($server, $fd, '3');
    }
}
