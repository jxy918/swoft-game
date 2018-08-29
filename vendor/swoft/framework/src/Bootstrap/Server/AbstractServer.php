<?php

namespace Swoft\Bootstrap\Server;

use Swoft\App;
use Swoft\Bean\Collector\SwooleListenerCollector;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Helper\StringHelper;
use Swoole\Lock;
use Swoole\Server;

/**
 * Abstract Server
 */
abstract class AbstractServer implements ServerInterface
{
    use ServerTrait;

    /**
     * TCP Setting
     *
     * @var array
     */
    public $tcpSetting = [];

    /**
     *  HTTP Setting
     *
     * @var array
     */
    public $httpSetting = [];

    /**
     * Server Setting
     *
     * @var array
     */
    public $serverSetting = [];

    /**
     * Swoole Setting
     *
     * @var array
     */
    public $setting = [];

    /**
     * Swoole server
     *
     * @var Server
     */
    protected $server;

    /**
     * Swoft entry file
     *
     * @var string
     */
    protected $scriptFile;

    /**
     * Worker lock
     *
     * @var Lock;
     */
    protected $workerLock;

    /**
     * AbstractServer constructor.
     *
     * @throws \InvalidArgumentException When any core server setting not exist, will throw this exception
     */
    public function __construct()
    {
        // Init App
        App::$server = $this;

        /** @var array[] $settings */
        $settings = App::getAppProperties()->get('server');

        $this->initSettings($settings);
    }

    /**
     * Register the event callback of swoole server
     */
    protected function registerSwooleServerEvents()
    {
        $swooleListeners = SwooleListenerCollector::getCollector();

        if (!isset($swooleListeners[SwooleEvent::TYPE_SERVER]) || empty($swooleListeners[SwooleEvent::TYPE_SERVER])) {
            return;
        }

        $swooleServerListeners = $swooleListeners[SwooleEvent::TYPE_SERVER];
        $this->registerSwooleEvents($this->server, $swooleServerListeners);
    }

    /**
     * Register swoole server events
     *
     * @param Server $handler
     * @param array  $events
     */
    protected function registerSwooleEvents($handler, array $events)
    {
        foreach ($events as $event => $beanName) {
            $object = bean($beanName);
            $method = SwooleEvent::getHandlerFunction($event);
            $handler->on($event, [$object, $method]);
        }
    }

    /**
     * Reload workers
     *
     * @param bool $onlyTask Only reload TaskWorkers
     */
    public function reload($onlyTask = false)
    {
        $signal = $onlyTask ? SIGUSR2 : SIGUSR1;
        posix_kill($this->serverSetting['managerPid'], $signal);
    }

    /**
     * Stop server
     */
    public function stop(): bool
    {
        $timeout = 60;
        $startTime = time();
        $this->serverSetting['masterPid'] && posix_kill($this->serverSetting['masterPid'], SIGTERM);

        $result = true;
        while (1) {
            $masterIslive = $this->serverSetting['masterPid'] && posix_kill($this->serverSetting['masterPid'], SIGTERM);
            if ($masterIslive) {
                if (time() - $startTime >= $timeout) {
                    $result = false;
                    break;
                }
                usleep(10000);
                continue;
            }

            break;
        }
        return $result;
    }

    /**
     * Is server running ?
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $masterIsLive = false;
        $pFile = $this->serverSetting['pfile'];

        // Is pid file exist ?
        if (file_exists($pFile)) {
            // Get pid file content and parse the content
            $pidFile = file_get_contents($pFile);
            $pids = explode(',', $pidFile);

            $this->serverSetting['masterPid'] = $pids[0];
            $this->serverSetting['managerPid'] = $pids[1];
            $masterIsLive = $this->serverSetting['masterPid'] && @posix_kill($this->serverSetting['managerPid'], 0);
        }

        return $masterIsLive;
    }

    /**
     * Get server
     *
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * Get TCP setting
     *
     * @return array
     */
    public function getTcpSetting(): array
    {
        return $this->tcpSetting;
    }

    /**
     * Get HTTP setting
     *
     * @return array
     */
    public function getHttpSetting(): array
    {
        return $this->httpSetting;
    }

    /**
     * Get Server setting
     *
     * @return array
     */
    public function getServerSetting(): array
    {
        return $this->serverSetting;
    }

    /**
     * @return Lock
     */
    public function getWorkerLock(): Lock
    {
        return $this->workerLock;
    }

    /**
     * @param Lock $workerLock
     */
    public function setWorkerLock(Lock $workerLock)
    {
        $this->workerLock = $workerLock;
    }

    /**
     * Init settings
     *
     * @param array[] $settings
     * @throws \InvalidArgumentException
     */
    public function initSettings(array $settings)
    {
        if (!isset($settings['tcp'])) {
            throw new \InvalidArgumentException('Tcp startup parameter is not configured，settings=' . \json_encode($settings));
        }

        if (!isset($settings['http'])) {
            throw new \InvalidArgumentException('HTTP startup parameters is not configured, settings=' . \json_encode($settings));
        }

        if (!isset($settings['server'])) {
            throw new \InvalidArgumentException('server startup parameters is not configured, settings=' . \json_encode($settings));
        }

        if (!isset($settings['setting'])) {
            throw new \InvalidArgumentException('"setting" parameters is not configured, settings=' . \json_encode($settings));
        }

        foreach ($settings['setting'] as $key => $value) {
            // path alias
            if ($value && \is_string($value) && $value[0] === '@') {
                $settings['setting'][$key] = App::getAlias($value);
            }
        }

        $this->setting = $settings['setting'];
        $this->tcpSetting = $settings['tcp'];
        $this->httpSetting = $settings['http'];
        $this->serverSetting = $settings['server'];

        // fix bug must to int
        if (isset($this->setting['task_ipc_mode'])) {
            $this->setting['task_ipc_mode'] = (int)$this->setting['task_ipc_mode'];
        }
        if (isset($this->setting['message_queue_key'])) {
            $this->setting['message_queue_key'] = (int)$this->setting['message_queue_key'];
        }
    }

    /**
     * Get TCP listen setting
     *
     * @return array
     */
    protected function getListenTcpSetting(): array
    {
        $listenTcpSetting = $this->tcpSetting;
        unset($listenTcpSetting['host'], $listenTcpSetting['port'], $listenTcpSetting['mode'], $listenTcpSetting['type']);
        return $listenTcpSetting;
    }

    /**
     * Set server to Daemonize
     *
     * @return $this
     */
    public function setDaemonize()
    {
        $this->setting['daemonize'] = 1;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDaemonize(): bool
    {
        return (int)$this->setting['daemonize'] === 1;
    }

    /**
     * Get pname
     *
     * @return string
     */
    public function getPname(): string
    {
        return $this->serverSetting['pname'];
    }

    /**
     * @return string
     */
    public function getServerType(): string
    {
        return $this->serverSetting['server_type'] ?? 'unknown';
    }
}
