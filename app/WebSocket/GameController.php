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
use App\Game\Conf\GameConst;

use App\Middlewares\GameMiddleware;

/**
 * Class GameController - This is an controller for handle websocket
 * @package App\WebSocket
 * @WebSocket("/game")
 */
class GameController implements HandlerInterface
{
    /**
     * fd和用户uid信息的映射关系
     * @var array
     */
    private $user_map = array();

    /**
     * 用户登陆信息，其中有用户的fd
     * @var array
     */
    private $user_info = array();
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
            $data = isset($this->user_info[$uinfo['account']]) ? $this->user_info[$uinfo['account']] : array();
            //之前信息存在， 清除之前的连接
            if(!empty($data)) {
                //$data = json_decode($data, true);
                if(isset($data['fd'])) {
                    //处理双开的情况
                    $this->loginFail($server, $data['fd'], '1');
                    $server->close($data['fd']);
                    unset($this->user_map[$data['fd']]);
                    unset($this->user_info[$data['account']]);
                }
            }
            //保存登陆信息
            $this->user_info[$uinfo['account']] = $uinfo;
            //现删除变量数组里之前的值
            $this->user_map[$fd] = $uinfo['account'];
        } else {
            $this->loginFail($server, $fd, '2');
        }
        //var_dump('user_map',$this->user_map, 'user_info', $this->user_info);
    }

    /**
     * @param Server $server
     * @param Frame $frame
     * @return mixed
     */
    public function onMessage(Server $server, Frame $frame)
    {
        $data = Packet::packDecode($frame->data);
        if(isset($data['code']) && $data['code'] == 0 && isset($data['msg']) && $data['msg'] == 'OK') {
            Log::show('Recv <<<  cmd='.$data['cmd'].'  scmd='.$data['scmd'].'  len='.$data['len'].'  data='.json_encode($data['data']));
            //转发请求，代理模式处理,websocket路由到相关逻辑
            $data['serv'] = $server;
            $data['protocol'] = GameConst::GM_PROTOCOL_WEBSOCK;
            $obj = new Dispatch($data);
            $back = '';
            if(!empty($obj->getStrategy())) {
                $back = $obj->exec();
                $server->push($frame->fd, $back, WEBSOCKET_OPCODE_BINARY);
            } else {
                if ($data['protocol'] == GameConst::GM_PROTOCOL_HTTP) {;
                    $back = "<center><h1>404 Not Found </h1></center><hr><center>swoole/2.1.3</center>\n";
                }
            }
            Log::show('Tcp Strategy <<<  data='.$back, GameConst::GM_LOG_LEVEL_DEBUG);
        } else {
            Log::show($data['msg']);
        }
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
        if(isset($this->user_map[$fd])) {
            $key = $this->user_map[$fd];
            unset($this->user_map[$fd]);
            if(isset($this->user_info[$key])) {
                unset($this->user_info[$key]);
            }
        }
    }
}
