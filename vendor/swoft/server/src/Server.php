<?php declare(strict_types=1);

namespace Swoft\Server;

use InvalidArgumentException;
use Swoft;
use Swoft\Co;
use Swoft\Console\Console;
use Swoft\Http\Server\HttpServer;
use Swoft\Log\Helper\CLog;
use Swoft\Server\Contract\ServerInterface;
use Swoft\Server\Event\ServerStartEvent;
use Swoft\Server\Event\WorkerEvent;
use Swoft\Server\Exception\ServerException;
use Swoft\Server\Helper\ServerHelper;
use Swoft\Stdlib\Helper\Dir;
use Swoft\Stdlib\Helper\Str;
use Swoft\Stdlib\Helper\Sys;
use Swoft\WebSocket\Server\WebSocketServer;
use Swoole\Coroutine;
use Swoole\Process;
use Swoole\Runtime;
use Swoole\Server as CoServer;
use function swoole_cpu_num;
use Throwable;
use function alias;
use function array_diff;
use function array_keys;
use function array_merge;
use function dirname;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function get_class;
use function range;
use function sprintf;
use function srun;
use function ucfirst;
use const SWOOLE_PROCESS;
use const SWOOLE_SOCK_TCP;

/**
 * Class Server
 *
 * @since 2.0
 */
abstract class Server implements ServerInterface
{
    /**
     * Swoft server
     *
     * @var Server|HttpServer|WebSocketServer
     */
    private static $server;

    /**
     * Server type name. eg: http, ws, tcp ...
     *
     * @var string
     */
    protected static $serverType = 'TCP';

    /**
     * Default port
     *
     * @var int
     */
    protected $port = 80;

    /**
     * Default host address
     *
     * @var string
     */
    protected $host = '0.0.0.0';

    /**
     * Default mode type
     *
     * @var int
     */
    protected $mode = SWOOLE_PROCESS;

    /**
     * Default socket type
     *
     * @var int
     */
    protected $type = SWOOLE_SOCK_TCP;

    /**
     * Server setting for swoole. (@see swooleServer->setting)
     *
     * @link https://wiki.swoole.com/wiki/page/274.html
     * @var array
     */
    protected $setting = [];

    /**
     * The server unique name
     *
     * @var string
     */
    protected $pidName = 'swoft';

    /**
     * Pid file
     *
     * @var string
     */
    protected $pidFile = '@runtime/swoft.pid';

    /**
     * @var string
     */
    protected $commandFile = '@runtime/swoft.command';

    /**
     * Record started server PIDs and with current workerId
     *
     * @var array
     */
    private $pidMap = [
        'masterPid'  => 0,
        'managerPid' => 0,
        // if = 0, current is at master/manager process.
        'workerPid'  => 0,
        // if < 0, current is at master/manager process.
        'workerId'   => -1,
    ];

    /**
     * Server event for swoole event
     *
     * @var array
     *
     * @example
     * [
     *     'serverName' => new SwooleEventListener(),
     *     'serverName' => new SwooleEventListener(),
     *     'serverName' => new SwooleEventListener(),
     * ]
     */
    protected $on = [];

    /**
     * Add port listener
     *
     * @var array
     * @example
     * [
     *    'name' => ServerInterface,
     *    'name2' => ServerInterface,
     * ]
     */
    protected $listener = [];

    /**
     * Add process
     *
     * @var array
     *
     * @example
     * [
     *     'name' => UserProcessInterface,
     *     'name2' => UserProcessInterface,
     * ]
     */
    protected $process = [];

    /**
     * Script file
     *
     * @var string
     */
    protected $scriptFile = '';

    /**
     * Swoole Server
     *
     * @var CoServer|\Swoole\Http\Server|\Swoole\Websocket\Server
     */
    protected $swooleServer;

    /**
     * Debug level
     *
     * @var integer
     */
    private $debug = 0;

    /**
     * Server id
     *
     * @var string
     */
    private $id = '';

    /**
     * Server unique id
     *
     * @var string
     */
    private $uniqid = '';

    /**
     * @var string
     */
    private $fullCommand = '';

    /**
     * Server constructor
     */
    public function __construct()
    {
        // Init default settings
        $this->setting = $this->defaultSetting();

        // Init
        $this->init();
    }

    /**
     * Init
     */
    public function init(): void
    {
        $this->uniqid = Str::uniqID('', true);
    }

    /**
     * @return string
     */
    public function getServerType(): string
    {
        return static::$serverType;
    }

    /**
     * On master start event
     *
     * @param CoServer $server
     *
     * @return void
     * @throws Throwable
     */
    public function onStart(CoServer $server): void
    {
        // Save PID to property
        $this->setPidMap($server);

        $masterPid  = $server->master_pid;
        $managerPid = $server->manager_pid;

        $pidStr = sprintf('%s,%s', $masterPid, $managerPid);
        $title  = sprintf('%s master process (%s)', $this->pidName, $this->scriptFile);

        // Save PID to file
        $pidFile = alias($this->pidFile);
        Dir::make(dirname($pidFile));
        file_put_contents($pidFile, $pidStr);

        // Save pull command to file
        $commandFile = alias($this->commandFile);
        Dir::make(dirname($commandFile));
        file_put_contents($commandFile, $this->fullCommand);

        // Set process title
        Sys::setProcessTitle($title);

        // Use `go` to open coroutine
        Coroutine::create(function () use ($server) {
            // Before
            Swoft::trigger(ServerEvent::BEFORE_START_EVENT, $this, $server);

            // Trigger
            Swoft::trigger(new ServerStartEvent(SwooleEvent::START, $server), $this);

            // After event
            Swoft::trigger(ServerEvent::AFTER_EVENT, $this);
        });
    }

    /**
     * Manager start event
     *
     * @param CoServer $server
     *
     * @throws Throwable
     */
    public function onManagerStart(CoServer $server): void
    {
        // Server pid map
        $this->setPidMap($server);

        // Set process title
        Sys::setProcessTitle(sprintf('%s manager process', $this->pidName));

        // NOTICE: Swoole not support to open coroutine on manager process
        Swoft::trigger(new ServerStartEvent(SwooleEvent::MANAGER_START, $server), $this);
    }

    /**
     * Manager stop event
     *
     * @param CoServer $server
     *
     * @throws Throwable
     */
    public function onManagerStop(CoServer $server): void
    {
        // NOTICE: Swoole not support to open coroutine on manager process
        Swoft::trigger(new ServerStartEvent(SwooleEvent::MANAGER_STOP, $server), $this);
    }

    /**
     * Shutdown event
     *
     * @param CoServer $server
     *
     * @throws Throwable
     */
    public function onShutdown(CoServer $server): void
    {
        $this->log("Shutdown: pidFile={$this->pidFile}");

        // Delete pid file
        ServerHelper::removePidFile(alias($this->pidFile));

        // Delete command file
        ServerHelper::removePidFile(alias($this->commandFile));

        // Use `Scheduler` to open coroutine
        srun(function () use ($server) {
            // Before
            Swoft::trigger(ServerEvent::BEFORE_SHUTDOWN_EVENT, $this, $server);

            // Trigger event
            Swoft::trigger(new ServerStartEvent(SwooleEvent::SHUTDOWN, $server), $this);

            // After event
            Swoft::trigger(ServerEvent::AFTER_EVENT, $this);
        });
    }

    /**
     * Worker start event
     *
     * @param CoServer $server
     * @param int      $workerId
     *
     * @throws Throwable
     */
    public function onWorkerStart(CoServer $server, int $workerId): void
    {
        // Save PID and workerId
        $this->pidMap['workerId']  = $workerId;
        $this->pidMap['workerPid'] = $server->worker_pid;

        $event = new WorkerEvent(SwooleEvent::WORKER_START, $server, $workerId);
        // Is task process
        $isTaskProcess      = $workerId >= $server->setting['worker_num'];
        $event->taskProcess = $isTaskProcess;

        Swoft::trigger($event);

        // Task process
        if ($isTaskProcess) {
            $procRole  = 'task';
            $eventName = ServerEvent::TASK_PROCESS_START;
            // Worker process
        } else {
            $procRole  = 'worker';
            $eventName = ServerEvent::WORK_PROCESS_START;
        }

        // For special role process
        $newEvent = clone $event;
        $newEvent->setName($eventName);

        Sys::setProcessTitle(sprintf('%s %s process', $this->pidName, $procRole));

        // In coroutine, sync task is not in coroutine
        if (Co::id() > 0) {
            // Before
            Swoft::trigger(ServerEvent::BEFORE_WORKER_START_EVENT, $this, $server, $workerId);

            // Already in coroutine
            Swoft::trigger($newEvent, $this);

            // After event
            Swoft::trigger(ServerEvent::AFTER_EVENT, $this);

            return ;
        }

        // Trigger task event
        Swoft::trigger($newEvent, $this);
    }

    /**
     * Worker stop event
     *
     * @param CoServer $server
     * @param int      $workerId
     *
     * @throws Throwable
     */
    public function onWorkerStop(CoServer $server, int $workerId): void
    {
        $this->log("WorkerStop: workerId=$workerId");

        $event = new WorkerEvent(SwooleEvent::WORKER_STOP, $server, $workerId);

        // is task process
        $event->taskProcess = $workerId >= $server->setting['worker_num'];

        // Use `Scheduler` to open coroutine
        srun(function () use ($event, $server, $workerId) {
            // Before
            Swoft::trigger(ServerEvent::BEFORE_WORKER_STOP_EVENT, $this, $server, $workerId);

            // Trigger
            Swoft::trigger($event, $this);

            // After event
            Swoft::trigger(ServerEvent::AFTER_EVENT, $this);
        });
    }

    /**
     * Worker error stop event(in manager process)
     *
     * @param CoServer $server
     * @param int      $workerId
     * @param int      $workerPid
     * @param int      $exitCode
     * @param int      $signal
     *
     * @throws Throwable
     */
    public function onWorkerError(CoServer $server, int $workerId, int $workerPid, int $exitCode, int $signal): void
    {
        $this->log("WorkerError: exitCode=$exitCode, Error worker: workerId=$workerId workerPid=$workerPid");

        $event = new WorkerEvent(SwooleEvent::WORKER_ERROR, $server, $workerId);

        // It's task process
        $event->taskProcess = $workerId >= $server->setting['worker_num'];
        $event->setParams([
            'signal'    => $signal,
            'exitCode'  => $exitCode,
            'workerPid' => $workerPid,
        ]);

        // NOTICE:
        //  WorkerError at manager process
        //  but swoole not support to open coroutine on manager process
        Swoft::trigger($event, $this);
    }

    /**
     * @return string
     */
    public function getPidName(): string
    {
        return $this->pidName;
    }

    /**
     * @param string $pidName
     */
    public function setPidName(string $pidName): void
    {
        $this->pidName = $pidName;
    }

    /**
     * Bind swoole event and start swoole server
     *
     * @throws ServerException
     * @throws Swoft\Bean\Exception\ContainerException
     */
    protected function startSwoole(): void
    {
        if (!$this->swooleServer) {
            throw new ServerException('You must to new server before start swoole!');
        }

        // Always enable coroutine hook on server
        CLog::info('Swoole\Runtime::enableCoroutine');
        Runtime::enableCoroutine();

        Swoft::trigger(ServerEvent::BEFORE_SETTING, $this);

        // Set settings
        $this->swooleServer->set($this->setting);
        // Update setting property
        // $this->setSetting($this->swooleServer->setting);

        // Before Add event
        Swoft::trigger(ServerEvent::BEFORE_ADDED_EVENT, $this);

        // Register events
        $defaultEvents = $this->defaultEvents();
        $swooleEvents  = array_merge($defaultEvents, $this->on);

        // Add events
        $this->addEvent($this->swooleServer, $swooleEvents, $defaultEvents);

        //After add event
        Swoft::trigger(ServerEvent::AFTER_ADDED_EVENT, $this);

        // Before listener
        Swoft::trigger(ServerEvent::BEFORE_ADDED_LISTENER, $this);

        // Add port listener
        $this->addListener();

        // Before bind process
        Swoft::trigger(ServerEvent::BEFORE_ADDED_PROCESS, $this);

        // Add Process
        Swoft::trigger(ServerEvent::ADDED_PROCESS, $this);

        // After bind process
        Swoft::trigger(ServerEvent::AFTER_ADDED_PROCESS, $this);

        // Trigger event
        Swoft::trigger(ServerEvent::BEFORE_START, $this, array_keys($swooleEvents));

        // Storage server instance
        self::$server = $this;

        // Start swoole server
        $this->swooleServer->start();
    }

    /**
     * Add listener serve to the main server
     *
     * @throws ServerException
     * @throws Swoft\Bean\Exception\ContainerException
     */
    protected function addListener(): void
    {
        foreach ($this->listener as $listener) {
            if (!$listener instanceof ServerInterface) {
                continue;
            }

            $host = $listener->getHost();
            $port = $listener->getPort();
            $type = $listener->getType();

            if (!$events = $listener->getOn()) {
                throw new ServerException(
                    'Not add any event handler for the listener server: ' . get_class($listener)
                );
            }

            /* @var CoServer\Port $server */
            $server = $this->swooleServer->listen($host, $port, $type);
            $server->set($listener->getSetting());

            // Bind events to the sub-server
            $this->addEvent($server, $events);

            // Trigger event
            Swoft::trigger(ServerEvent::AFTER_ADDED_LISTENER, $server, $this);
        }
    }

    /**
     * Add swoole events
     *
     * @param CoServer|CoServer\Port $server
     * @param array                  $swooleEvents
     * @param array                  $defaultEvents
     *
     * @throws ServerException
     */
    protected function addEvent($server, array $swooleEvents, array $defaultEvents = []): void
    {
        foreach ($swooleEvents as $name => $listener) {
            // Default events
            if (isset($defaultEvents[$name])) {
                $server->on($name, $listener);
                continue;
            }

            // Coroutine task and sync task
            if ($name === SwooleEvent::TASK) {
                $this->addTaskEvent($server, $listener, $name);
                continue;
            }

            if (!isset(SwooleEvent::LISTENER_MAPPING[$name])) {
                throw new ServerException(sprintf('Swoole %s event is not defined!', $name));
            }

            $listenerInterface = SwooleEvent::LISTENER_MAPPING[$name];
            if (!($listener instanceof $listenerInterface)) {
                throw new ServerException(sprintf('Swoole %s event listener is not %s', $name, $listenerInterface));
            }

            $listenerMethod = sprintf('on%s', ucfirst($name));
            $server->on($name, [$listener, $listenerMethod]);
        }
    }

    /**
     * @param CoServer|CoServer\Port $server
     * @param object                 $listener
     * @param string                 $name
     *
     * @throws ServerException
     */
    protected function addTaskEvent($server, $listener, string $name): void
    {
        $index = (int)$this->isCoroutineTask();

        $taskListener = SwooleEvent::LISTENER_MAPPING[$name][$index] ?? '';
        if (empty($taskListener)) {
            throw new ServerException(sprintf('Swoole %s event is not defined!', $name));
        }

        if (!$listener instanceof $taskListener) {
            throw new ServerException(sprintf('Swoole %s event listener is not %s', $name, $taskListener));
        }

        $listenerMethod = sprintf('on%s', ucfirst($name));
        $server->on($name, [$listener, $listenerMethod]);
    }

    /**
     * @return bool
     */
    public function isCoroutineTask(): bool
    {
        return $this->setting['task_enable_coroutine'] ?? false;
    }

    /**
     * @return string
     */
    public function getFullCommand(): string
    {
        return $this->fullCommand;
    }

    /**
     * @param string $fullCommand
     */
    public function setFullCommand(string $fullCommand): void
    {
        $this->fullCommand = $fullCommand;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     */
    public function setMode(int $mode): void
    {
        if (!isset(self::MODE_LIST[$mode])) {
            throw new InvalidArgumentException('invalid server mode value: ' . $mode);
        }

        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getModeName(): string
    {
        return self::MODE_LIST[$this->mode] ?? 'Unknown';
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        if (!isset(self::TYPE_LIST[$type])) {
            throw new InvalidArgumentException('invalid server type value: ' . $type);
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->type] ?? 'Unknown';
    }

    /**
     * @return array
     */
    public function getSetting(): array
    {
        return $this->setting;
    }

    /**
     * @param array $setting
     */
    public function setSetting(array $setting): void
    {
        $this->setting = array_merge($this->setting, $setting);
    }

    /**
     * @return array
     */
    public function getOn(): array
    {
        return $this->on;
    }

    /**
     * @param string $eventName
     *
     * @return bool
     */
    public function hasListener(string $eventName): bool
    {
        return isset($this->on[$eventName]);
    }

    /**
     * @return array
     */
    public function getRegisteredEvents(): array
    {
        return array_keys($this->on);
    }

    /**
     * @param string $scriptFile
     */
    public function setScriptFile(string $scriptFile): void
    {
        $this->scriptFile = $scriptFile;
    }

    /**
     * @return Server|HttpServer|WebSocketServer
     */
    public static function getServer(): ?Server
    {
        return self::$server;
    }

    /**
     * @param Server $server
     */
    public static function setServer(Server $server): void
    {
        self::$server = $server;
    }

    /**
     * Restart server
     */
    public function startWithDaemonize(): void
    {
        // Restart default is daemon
        $this->setDaemonize();

        // Start server
        $this->start();
    }

    /**
     * Quick restart
     *
     * @throws Swoft\Exception\SwoftException
     */
    public function restart(): void
    {
        if ($this->isRunning()) {
            // Restart command
            $command = Co::readFile(alias($this->commandFile));

            // Stop server
            $this->stop();

            // Exe restart shell
            Coroutine::exec($command);

            CLog::info('Restart success(%s)!', $command);
        }
    }

    /**
     * @param bool $onlyTaskWorker
     *
     * @return bool
     */
    public function reload(bool $onlyTaskWorker = false): bool
    {
        if (($pid = $this->pidMap['masterPid']) < 1) {
            return false;
        }

        // SIGUSR1(10):
        //  Send a signal to the management process that will smoothly restart all worker processes
        // SIGUSR2(12):
        //  Send a signal to the management process, only restart the task process
        $signal = $onlyTaskWorker ? 12 : 10;

        return ServerHelper::sendSignal($pid, $signal);
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        $pid = $this->getPid();
        if ($pid < 1) {
            return false;
        }

        // SIGTERM = 15
        if (ServerHelper::killAndWait($pid, 15, $this->pidName, 30)) {
            $rmPidOk = ServerHelper::removePidFile(alias($this->pidFile));
            $rmCmdOk = ServerHelper::removePidFile(alias($this->commandFile));

            return $rmPidOk && $rmCmdOk;
        }

        return false;
    }

    /**
     * Shutdown server
     */
    public function shutdown(): void
    {
        $this->swooleServer->shutdown();
    }

    /**
     * Stop the worker process and immediately trigger the onWorkerStop callback function
     *
     * @param int  $workerId
     * @param bool $waitEvent
     *
     * @return bool
     */
    public function stopWorker(int $workerId = -1, bool $waitEvent = false): bool
    {
        if ($workerId > -1 && $this->swooleServer) {
            return $this->swooleServer->stop($workerId, $waitEvent);
        }

        return false;
    }

    /**
     * Response data to client by socket connection
     *
     * @param int    $fd
     * @param string $data
     * param int $length
     *
     * @return bool
     */
    public function writeTo(int $fd, string $data): bool
    {
        return $this->swooleServer->send($fd, $data);
    }

    /**
     * @param int $fd
     *
     * @return bool
     */
    public function exist(int $fd): bool
    {
        return $this->swooleServer->exist($fd);
    }

    /**
     * Print log message to terminal
     *
     * @param string $msg
     * @param array  $data
     * @param string $type
     */
    public function log(string $msg, array $data = [], string $type = 'info'): void
    {
        if ($this->isDaemonize()) {
            return;
        }

        if ($this->debug > 0) {
            $tid = Co::tid();
            $cid = Co::id();
            $wid = $this->getPid('workerId');

            Console::log("[WorkerId:$wid, TID:$tid, CID:$cid] " . $msg, $data, $type);
        }
    }

    /**
     * Check if the server is running
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $pidFile = alias($this->pidFile);

        // Is pid file exist ?
        if (file_exists($pidFile)) {
            // Get pid file content and parse the content
            $pidFile = file_get_contents($pidFile);

            // Parse and record PIDs
            [$masterPID, $managerPID] = explode(',', $pidFile);
            // Format type
            $masterPID  = (int)$masterPID;
            $managerPID = (int)$managerPID;

            $this->pidMap['masterPid']  = $masterPID;
            $this->pidMap['managerPid'] = $managerPID;

            // Notice: skip pid 1, resolve start server on docker.
            return $masterPID > 1 && Process::kill($masterPID, 0);
        }

        return false;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->swooleServer->connections);
    }

    /**
     * Send message to notify workers, like swooleServer->sendMessage().
     *
     * @param mixed $data
     * @param array $dstWIDs
     * @param array $excludeWIDs
     */
    public function notifyWorkers($data, array $dstWIDs = [], array $excludeWIDs = []): void
    {
        // Send to all workers
        if (!$dstWIDs) {
            $dstWIDs = range(0, $this->swooleServer->setting['worker_num'] - 1);
        }

        if ($excludeWIDs) {
            $dstWIDs = array_diff($dstWIDs, $excludeWIDs);
        }

        foreach ($dstWIDs as $wid) {
            $this->swooleServer->sendMessage($data, $wid);
        }
    }

    /**
     * @return array
     */
    public function getPidMap(): array
    {
        return $this->pidMap;
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function getPid(string $name = 'masterPid'): int
    {
        return $this->pidMap[$name] ?? 0;
    }

    /**
     * @return string
     */
    public function getPidFile(): string
    {
        return $this->pidFile;
    }

    /**
     * @return \Swoole\Http\Server|CoServer|\Swoole\Websocket\Server
     */
    public function getSwooleServer()
    {
        return $this->swooleServer;
    }

    /**
     * @return string
     */
    public function getCommandFile(): string
    {
        return $this->commandFile;
    }

    /**
     * @param string $commandFile
     */
    public function setCommandFile(string $commandFile): void
    {
        $this->commandFile = $commandFile;
    }

    /**
     * @return array
     */
    public function getSwooleStats(): array
    {
        return $this->swooleServer->stats();
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug > 0;
    }

    /**
     * @param int $debug
     */
    public function setDebug($debug): void
    {
        $this->debug = (int)$debug;
    }

    /**
     * Set server, run server on the background
     *
     * @param bool $yes
     *
     * @return $this
     */
    public function setDaemonize(bool $yes = true): self
    {
        $this->setting['daemonize'] = $yes ? 1 : 0;
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
     * @return int
     */
    public function getErrorNo(): int
    {
        return $this->swooleServer->getLastError();
    }

    /**
     * @param int $fd
     *
     * @return array
     */
    public function getClientInfo(int $fd): array
    {
        return (array)$this->swooleServer->getClientInfo($fd);
    }

    /**
     * Set pid map
     *
     * @param CoServer $server
     */
    protected function setPidMap(CoServer $server): void
    {
        if ($server->master_pid > 0) {
            $this->pidMap['masterPid'] = $server->master_pid;
        }

        if ($server->manager_pid > 0) {
            $this->pidMap['managerPid'] = $server->manager_pid;
        }
    }

    /**
     * @return array
     */
    public function getListener(): array
    {
        return $this->listener;
    }

    /**
     * @return array
     */
    public function defaultEvents(): array
    {
        return [
            SwooleEvent::START         => [$this, 'onStart'],
            SwooleEvent::SHUTDOWN      => [$this, 'onShutdown'],
            SwooleEvent::MANAGER_START => [$this, 'onManagerStart'],
            SwooleEvent::MANAGER_STOP  => [$this, 'onManagerStop'],
            SwooleEvent::WORKER_START  => [$this, 'onWorkerStart'],
            SwooleEvent::WORKER_STOP   => [$this, 'onWorkerStop'],
            SwooleEvent::WORKER_ERROR  => [$this, 'onWorkerError'],
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUniqid(): string
    {
        return $this->uniqid;
    }

    /**
     * @return array
     */
    public function getProcess(): array
    {
        return $this->process;
    }

    /**
     * @return array
     */
    protected function defaultSetting(): array
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return [
            'daemonize'       => 0,
            'worker_num'      => swoole_cpu_num(),

            // If > 0, must listen event: task, finish
            'task_worker_num' => 0
        ];
    }
}
