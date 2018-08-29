<?php

namespace Swoft\Bootstrap\Server;

use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Collector\ServerListenerCollector;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Core\ApplicationContext;
use Swoft\Core\InitApplicationContext;
use Swoft\Event\AppEvent;
use Swoft\Helper\ProcessHelper;
use Swoft\Pipe\PipeMessage;
use Swoft\Pipe\PipeMessageInterface;
use Swoole\Server;

/**
 * Server trait
 */
trait ServerTrait
{
    /**
     * Before swoole server start
     */
    protected function beforeServerStart()
    {
        $this->fireServerEvent(SwooleEvent::ON_BEFORE_START, [$this]);
    }

    /**
     * onStart event callback
     *
     * @param Server $server
     * @throws \InvalidArgumentException
     */
    public function onStart(Server $server)
    {
        \file_put_contents($this->serverSetting['pfile'], $server->master_pid . ',' . $server->manager_pid);

        ProcessHelper::setProcessTitle($this->serverSetting['pname'] . ' master process (' . $this->scriptFile . ')');

        $this->fireServerEvent(SwooleEvent::ON_START, [$server]);
    }

    /**
     * onManagerStart event callback
     *
     * @param Server $server
     * @throws \InvalidArgumentException
     */
    public function onManagerStart(Server $server)
    {
        $this->fireServerEvent(SwooleEvent::ON_MANAGER_START, [$server]);

        ProcessHelper::setProcessTitle($this->serverSetting['pname'] . ' manager process');
    }

    /**
     * OnWorkerStart event callback
     *
     * @param Server $server server
     * @param int $workerId workerId
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    public function onWorkerStart(Server $server, int $workerId)
    {
        // Init Worker and TaskWorker
        $setting = $server->setting;
        $isWorker = false;

        if ($workerId >= $setting['worker_num']) {
            // TaskWorker
            ApplicationContext::setContext(ApplicationContext::TASK);
            ProcessHelper::setProcessTitle($this->serverSetting['pname'] . ' task process');
        } else {
            // Worker
            $isWorker = true;
            ApplicationContext::setContext(ApplicationContext::WORKER);
            ProcessHelper::setProcessTitle($this->serverSetting['pname'] . ' worker process');
        }

        $this->beforeWorkerStart($server, $workerId, $isWorker);

        $this->fireServerEvent(SwooleEvent::ON_WORKER_START, [$server, $workerId, $isWorker]);
    }

    /**
     * onPipeMessage event callback
     *
     * @param \Swoole\Server $server
     * @param int            $srcWorkerId
     * @param string         $message
     * @return void
     * @throws \InvalidArgumentException
     */
    public function onPipeMessage(Server $server, int $srcWorkerId, string $message)
    {
        /* @var PipeMessageInterface $pipeMessage */
        $pipeMessage = App::getBean(PipeMessage::class);
        list($type, $data) = $pipeMessage->unpack($message);

        App::trigger(AppEvent::PIPE_MESSAGE, null, $type, $data, $srcWorkerId);
    }

    /**
     * @param string $scriptFile
     */
    public function setScriptFile(string $scriptFile)
    {
        $this->scriptFile = $scriptFile;
    }

    /**
     * @param \Swoole\Server $server
     * @param int $workerId
     * @param bool $isWorker
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    private function beforeWorkerStart(Server $server, int $workerId, bool $isWorker)
    {
        // Load bean
        $this->reloadBean($isWorker);
    }

    /**
     * @param bool $isWorker
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function reloadBean(bool $isWorker)
    {
        BeanFactory::reload();
        $initApplicationContext = new InitApplicationContext();
        $initApplicationContext->init();

        if($isWorker && $this->workerLock->trylock() && env('AUTO_REGISTER', false)){
            App::trigger(AppEvent::WORKER_START);
        }
    }

    /**
     * fire server event listeners
     *
     * @param string $event
     * @param array  $params
     */
    protected function fireServerEvent(string $event, array $params)
    {
        /** @var array[] $collector */
        $collector = ServerListenerCollector::getCollector();

        if (!isset($collector[$event]) || empty($collector[$event])) {
            return;
        }

        foreach ($collector[$event] as $beanClass) {
            $listener = App::getBean($beanClass);
            $method = SwooleEvent::getHandlerFunction($event);
            $listener->$method(...$params);
        }
    }
}
