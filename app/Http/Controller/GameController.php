<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Controller;

use Swoft;
use Swoft\Http\Message\Response;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Throwable;
use Swoole\Coroutine\Http\Client;

use App\WebSocket\Game\Core\Packet;
use App\WebSocket\Game\Conf\MainCmd;
use App\WebSocket\Game\Conf\SubCmd;
use function server;
use const WEBSOCKET_OPCODE_BINARY;

/**
 * Class GameController
 * @Controller(prefix="/game")
 */
class GameController{
    /**
     * 用户信息
     * @var null
     */
    public $userinfo = array();

    /**
     * consul 发现服务ip
     */
    const DISCOVERY_IP = '192.168.7.197';

    /**
     * consul 发现服务port
     */
    const DISCOVERY_PORT = 8500;

    /**
     * consul 发现服务uri
     */
    const DISCOVERY_URI = '/v1/health/service/%s?passing=1&dc=dc1&near';

    /**
     * game input
     * @RequestMapping(route="/game")
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response)
    {
        if(!$this->_isLogin($request)) {
            return $response->redirect('/login');
        }
        return $response->redirect('/test');
    }

    /**
     * login
     * @RequestMapping(route="/login")
     * @param Request $request
     * @param Response $response
     * @return Swoft\Http\Message\Concern\CookiesTrait|Response
     * @throws Throwable
     */
    public function login(Request $request, Response $response)
    {
        $action = $request->post('action');
        $account = $request->post('account');
        $tips = '';
        if($action == 'login') {
            if(!empty($account)) {
                //注册登录
                $uinfo = array('account'=>$account);
                $response = $response->withCookie('USER_INFO', json_encode($uinfo));
                return $response->redirect('/test');
            } else {
                $tips = '温馨提示：用户账号不能为空！';
            }
        }
        return view('game/login', ['tips'=>$tips]);
    }

    /**
     * @RequestMapping(route="/test")
     * @return Response
     * @throws Throwable
     */
    public function test()
    {
        return view('game/test');
    }

    /**
     * 是否登录
     * @return bool
     */
    private function _isLogin(Request $request)
    {
        $cookie_info = $request->getCookieParams();
        if(isset($cookie_info['USER_INFO'])) {
            $this->userinfo = json_decode($cookie_info['USER_INFO']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 广播当前服务器消息
     * @RequestMapping(route="/broadcast")
     * @return array
     */
    public function broadcast(Request $request)
    {
        $msg = $request->query('msg');
        //原封不动发回去
        if(!empty($msg)) {
           $msg = array('data' => $msg);
        } else {
            $msg = array('data' => 'this is a system msg');
        }
        $data = Packet::packFormat('OK', 0, $msg);
        $data = Packet::packEncode($data, MainCmd::CMD_SYS, SubCmd::BROADCAST_MSG_RESP);
        $cnt = server()->sendToAll($data,  0,  50, WEBSOCKET_OPCODE_BINARY);
        return ['status'=>0, 'msg'=>'给'.$cnt.'广播了消息'];
    }

    /**
     * 广播全服, 分布式广播, 需要安装consul注册发现服务器
     * @RequestMapping(route="/broadcast_to_all")
     * @return array
     */
    public function broadcastToAll(Request $request)
    {
        $msg = $request->query('msg');
        $msg = !empty($msg) ? $msg : "this is a system msg";
        //走consul注册发现服务器来广播消息，获取服务器列表
        $serviceList = $this->getServiceList();
        $result = [];
        //采用http循环发送消息
        foreach($serviceList as $v) {
            $cli = new Client($v['ip'], $v['port']);
            $cli->get("broadcast?msg={$msg}");
            $result = $cli->body;
            $cli->close();
        }
        return $result;
    }

    /**
     * get service list, 默认就是游戏网关服务器的consul服务器name
     *
     * @param string $serviceName
     * @return array
     */
    public function getServiceList($serviceName = 'gateway')
    {
        $cli = new Client(self::DISCOVERY_IP, self::DISCOVERY_PORT);
        $cli->get(sprintf(self::DISCOVERY_URI, $serviceName));
        $result = $cli->body;
        $cli->close();
        $services   = json_decode($result, true);

        // 数据格式化
        $nodes = [];
        foreach ($services as $service) {
            if (!isset($service['Service'])) {
                App::warning("consul[Service] 服务健康节点集合，数据格式不不正确，Data=" . $result);
                continue;
            }
            $serviceInfo = $service['Service'];
            if (!isset($serviceInfo['Address'], $serviceInfo['Port'])) {
                App::warning("consul[Address] Or consul[Port] 服务健康节点集合，数据格式不不正确，Data=" . $result);
                continue;
            }
            $address = $serviceInfo['Address'];
            $port    = $serviceInfo['Port'];
            $uri     =  ['ip'=>$address, 'port'=>$port];
            $nodes[] = $uri;
        }
        return $nodes;
    }

    /**
     * 录像页面
     * @RequestMapping(route="/camera")
     * @return Response
     * @throws Throwable
     */
    public function camera()
    {
        return view('vedio/camera');
    }

    /**
     * 直播页面
     * @RequestMapping(route="/show")
     * @return Response
     * @throws Throwable
     */
    public function show()
    {
        return view('vedio/show');
    }
}