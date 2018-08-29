<?php

namespace Swoft\Rpc\Server\Rpc;

use Swoft\Bean\Collector\SwooleListenerCollector;
use Swoft\Bootstrap\SwooleEvent;
use Swoole\Server;
use Swoft\Bootstrap\Server\AbstractServer;

/**
 * RPC Server
 */
class RpcServer extends AbstractServer
{

    /**
     * Start server
     */
    public function start()
    {
        $this->server = new Server($this->tcpSetting['host'], $this->tcpSetting['port'], $this->tcpSetting['mode'], $this->tcpSetting['type']);

        // Bind event callback
        $listenSetting = $this->getListenTcpSetting();
        $setting = array_merge($this->setting, $listenSetting);
        $this->server->set($setting);
        $this->server->on(SwooleEvent::ON_START, [$this, 'onStart']);
        $this->server->on(SwooleEvent::ON_WORKER_START, [$this, 'onWorkerStart']);
        $this->server->on(SwooleEvent::ON_MANAGER_START, [$this, 'onManagerStart']);
        $this->server->on(SwooleEvent::ON_PIPE_MESSAGE, [$this, 'onPipeMessage']);

        $swooleEvents = $this->getSwooleEvents();
        $this->registerSwooleEvents($this->server, $swooleEvents);

        // before start
        $this->beforeServerStart();
        $this->server->start();
    }

    /**
     * @return array
     */
    private function getSwooleEvents(): array
    {
        $swooleListeners = SwooleListenerCollector::getCollector();
        $portEvents = $swooleListeners[SwooleEvent::TYPE_PORT][0] ?? [];
        $serverEvents = $swooleListeners[SwooleEvent::TYPE_SERVER] ?? [];
        return array_merge($portEvents, $serverEvents);
    }
}
