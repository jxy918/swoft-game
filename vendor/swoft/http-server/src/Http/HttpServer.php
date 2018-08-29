<?php

namespace Swoft\Http\Server\Http;

use Swoft\App;
use Swoft\Bean\Collector\SwooleListenerCollector;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Exception\RuntimeException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Swoft\Bootstrap\Server\AbstractServer;

/**
 * Http Server
 */
class HttpServer extends AbstractServer
{
    /**
     * @var \Swoole\Server::$port tcp port
     */
    protected $listen;

    /**
     * Start Server
     *
     * @throws \Swoft\Exception\RuntimeException
     */
    public function start()
    {
        // add server type
        $this->serverSetting['server_type'] = self::TYPE_HTTP;

    	if (!empty($this->setting['open_http2_protocol'])) {
    		$this->httpSetting['type'] = SWOOLE_SOCK_TCP|SWOOLE_SSL;
		}

        $this->server = new Server($this->httpSetting['host'], $this->httpSetting['port'], $this->httpSetting['mode'], $this->httpSetting['type']);

        // Bind event callback
        $this->server->set($this->setting);
        $this->server->on(SwooleEvent::ON_START, [$this, 'onStart']);
        $this->server->on(SwooleEvent::ON_WORKER_START, [$this, 'onWorkerStart']);
        $this->server->on(SwooleEvent::ON_MANAGER_START, [$this, 'onManagerStart']);
        $this->server->on(SwooleEvent::ON_REQUEST, [$this, 'onRequest']);
        $this->server->on(SwooleEvent::ON_PIPE_MESSAGE, [$this, 'onPipeMessage']);

        // Start RPC Server
        if ((int)$this->serverSetting['tcpable'] === 1) {
            $this->registerRpcEvent();
        }

        $this->registerSwooleServerEvents();
        $this->beforeServerStart();
        $this->server->start();
    }

    /**
     * Register rpc event, swoft/rpc-server required
     *
     * @throws \Swoft\Exception\RuntimeException
     */
    protected function registerRpcEvent()
    {
        $swooleListeners = SwooleListenerCollector::getCollector();

        if (!isset($swooleListeners[SwooleEvent::TYPE_PORT][0]) || empty($swooleListeners[SwooleEvent::TYPE_PORT][0])) {
            throw new RuntimeException("Please use swoft/rpc-server, run 'composer require swoft/rpc-server'");
        }

        $this->listen = $this->server->listen($this->tcpSetting['host'], $this->tcpSetting['port'], $this->tcpSetting['type']);
        $tcpSetting = $this->getListenTcpSetting();
        $this->listen->set($tcpSetting);

        $swooleRpcPortEvents = $swooleListeners[SwooleEvent::TYPE_PORT][0];
        $this->registerSwooleEvents($this->listen, $swooleRpcPortEvents);
    }

    /**
     * onRequest event callback
     * Each request will create an coroutine
     *
     * @param Request $request
     * @param Response $response
     * @throws \InvalidArgumentException
     */
    public function onRequest(Request $request, Response $response)
    {
        // Initialize Request and Response and set to RequestContent
        $psr7Request = \Swoft\Http\Message\Server\Request::loadFromSwooleRequest($request);
        $psr7Response = new \Swoft\Http\Message\Server\Response($response);

        /** @var \Swoft\Http\Server\ServerDispatcher $dispatcher */
        $dispatcher = App::getBean('serverDispatcher');
        $dispatcher->dispatch($psr7Request, $psr7Response);
    }
}
