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

use ReflectionException;
use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Response;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Carbon\Carbon;
use Throwable;


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
     * consul 发现服务url
     */
    const DISCOVERY_PATH = 'http://127.0.0.1:8500/v1/health/service/%s?passing=1&dc=dc1&near';

    /**
     * game input
     * @RequestMapping(route="/game")
     * @param Request $request
     * @param Response $response
     * @return array|Response
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
                $response = $this->addCookie($request,  $response, 'USER_INFO', json_encode($uinfo), 60);
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
        $serv = App::$server->getServer();
        $conn_client = $this->pushToAll($serv, $data);
        return $conn_client;
    }

    /**
     * set a cookie
     * @param Request $request
     * @param Response $response
     * @param string $key
     * @param string $value
     * @param int $expire(m)
     */
    protected function addCookie(Request $request, Response $response,  $key = '',  $value = '', $expire = 0)
    {
//        $uri = $request->getUri();
//        $path = '/';
//        $domain = $uri->getHost();
//        $secure = strtolower($uri->getScheme()) === 'https';
//        $httpOnly = false;
//        $expire = Carbon::now()->addMinutes($expire);
//        $cookie = new Cookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
        $response = $response->withCookie($key, $value);
        return $response;
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

    /**
     * 广播全服
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
            $notify_url = "http://{$v}/broadcast?msg={$msg}";
            $httpClient = new Client();
            $result[$v]     = $httpClient->get($notify_url)->getResult();
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
        $httpClient = new Client();
        $url        = sprintf(self::DISCOVERY_PATH, $serviceName);
        $result     = $httpClient->get($url)->getResult();
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

            $uri     = implode(":", [$address, $port]);
            $nodes[] = $uri;
        }
        return $nodes;
    }
}