<?php
namespace App\Boot;

use Swoft\App;
use Swoft\Bean\Annotation\ServerListener;
use Swoft\Bootstrap\Listeners\Interfaces\BeforeStartInterface;
use Swoft\Bootstrap\Server\AbstractServer;
use Swoft\Bootstrap\SwooleEvent;
use Swoole\Server;

//自己定义的服务器逻辑
use App\Game\Core\Packet;
use App\Game\Core\Dispatch;
use App\Game\Core\Log;
use App\Game\Conf\GameConst;


/**
 * Class GameServerListener
 * @package App\Boot\Listener
 * @ServerListener(event=SwooleEvent::ON_BEFORE_START)
 */
class GameServerListener implements BeforeStartInterface
{
    /**
     * 监听服务器对象
     * @var null
     */
    private $_port = null;

    /**
     * 主服务器出发 before事件,创建自定自定义服务器
     * @var Server $server
     * */
    public function onBeforeStart(AbstractServer $server)
    {
        $this->create($server->getServer());
    }

    /**
     * 创建监听服务器
     * @param Server $serv
     */
    public function create(Server $serv)
    {
        //获取tcp的配置信息
        $settings = App::getAppProperties()->get('server');
        if (!isset($settings['tcp1'])) {
            throw new \InvalidArgumentException('Tcp startup parameter is not configured，settings=' . \json_encode($settings));
        }
        $tcpSettings = $settings['tcp1'];
        $this->_port = $serv->listen($tcpSettings['host'], $tcpSettings['port'], $tcpSettings['type']);
        $this->setPortSettings($tcpSettings);
        $this->addPortListeners();
        $this->writeServerInfo($settings);
    }

    /**
     * 显示服务器信息
     * @param $settings
     */
    public function writeServerInfo(array $settings) {
        $tips =  "                         Listen TCP Game Server Information                     \n";
        $tips .= "************************************************************************************\n";
        $tips .= "* TCP  | host: <note>{$settings['tcp1']['host']}</note>, port: <note>{$settings['tcp1']['port']}</note>, type: <note>{$settings['tcp1']['type']}</note>, worker: <note>{$settings['setting']['worker_num']}</note> (<note>Enabled</note>)\n";
        $tips .= "************************************************************************************\n";
        echo \style()->t($tips);
    }

    /**
     * 设置服务器配置参数， 和主服务器一致
     * @param array $tcpSettings
     */
    public function setPortSettings(array $tcpSettings)
    {
        unset($tcpSettings['host'], $tcpSettings['port'], $tcpSettings['port1'], $tcpSettings['mode'], $tcpSettings['type']);
        //发现问题, 这里设置参数默认应当是和主服务器一致, 不能设置, 一设置不能触发回调receive事件
        $this->_port->set($tcpSettings);
    }

    /**
     * 添加监听事件
     */
    public function addPortListeners()
    {
        $this->_port->on('connect', array($this, 'onConnect'));
        $this->_port->on('Receive', array($this, 'onReceive'));
        $this->_port->on('Close', array($this, 'onClose'));
    }

    //tcp连接回调
    public function onConnect(Server $serv, $fd) {
        Log::show("Connect: connected success". $fd);
    }

    //TCP的消息处理逻辑
    public function onReceive($serv, $fd, $from_id, $data) {
        Log::show(" Receive: client #{$fd} send success Mete: \n{");
        if(!empty($data)) {
            $data = Packet::packDecode($data);
            $back = '';
            if (isset($data['code']) && $data['code'] == 0 && isset($data['msg']) && $data['msg'] == 'OK') {
                Log::show('Recv <<<  cmd=' . $data['cmd'] . '  scmd=' . $data['scmd'] . '  len=' . $data['len'] . '  data=' . json_encode($data['data']));
                //转发请求，代理模式处理,websocket路由到相关逻辑
                $data['serv'] = $serv;
                $back = $this->dispatch($data);
                Log::show('Tcp Strategy <<<  data=' . $back);
            } else {
                Log::show($data['msg']);
            }
            if (!empty($back)) {
                $serv->send($fd, $back);
            }
        }
        Log::split('}');
    }

    //服务器关闭回调
    public function onClose(Server $serv, $fd) {
        Log::show("Close: connection close: {$fd}");
    }


    /**
     * 根据路由策略处理逻辑，并返回数据
     * @param $data
     * @return string
     */
    public function dispatch($data) {
        $obj = new Dispatch($data);
        $back = "<center><h1>404 Not Found </h1></center><hr><center>swoft</center>\n";
        if(!empty($obj->getStrategy())) {
            $back = $obj->exec();
        }
        return $back;
    }
}