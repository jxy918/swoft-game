<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Command;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Helper\Show;
use Swoft\Server\Command\BaseServerCommand;
use Swoft\Server\Exception\ServerException;
use Swoft\Tcp\Server\TcpServer;
use Throwable;
use function bean;
use function input;
use function output;

/**
 * Class TcpServerCommand
 *
 * @Command("tcp",
 *     coroutine=false,
 *     alias="tcpsrv",
 *     desc="provide some commands to manage swoft TCP server"
 * )
 */
class TcpServerCommand extends BaseServerCommand
{
    /**
     * Start the tcp server
     *
     * @CommandMapping(usage="{fullCommand} [-d|--daemon]")
     * @CommandOption("daemon", short="d", desc="Run server on the background", default="false", type="bool")
     *
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     * @throws Throwable
     * @example
     *   {fullCommand}
     *   {fullCommand} -d  Start server on background
     *
     */
    public function start(): void
    {
        $server = $this->createServer();

        // Check if it has started
        if ($server->isRunning()) {
            $masterPid = $server->getPid();
            output()->writeln("<error>The server have been running!(PID: {$masterPid})</error>");
            return;
        }

        // Startup settings
        $this->configStartOption($server);

        $settings = $server->getSetting();
        // Setting
        $workerNum = $settings['worker_num'];

        // Server startup parameters
        $mainHost = $server->getHost();
        $mainPort = $server->getPort();

        // Main server
        $panel = [
            'TCP' => [
                'listen' => $mainHost . ':' . $mainPort,
                'type'   => $server->getTypeName(),
                'mode'   => $server->getModeName(),
                'worker' => $workerNum,
            ],
        ];

        // Port Listeners
        $panel = $this->appendPortsToPanel($server, $panel);

        Show::panel($panel);

        output()->writef('<success>Tcp server start success !</success>');

        // Start the server
        $server->start();
    }

    /**
     * Reload worker processes
     *
     * @CommandMapping(usage="{fullCommand} [-t]")
     * @CommandOption("t", desc="Only to reload task processes, default to reload worker and task")
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function reload(): void
    {
        $server = $this->createServer();
        $script = input()->getScript();

        // Check if it has started
        if (!$server->isRunning()) {
            output()->writeln('<error>The server is not running! cannot reload</error>');
            return;
        }

        output()->writef('<info>Server %s is reloading</info>', $script);

        if ($reloadTask = input()->hasOpt('t')) {
            Show::notice('Will only reload task worker');
        }

        if (!$server->reload($reloadTask)) {
            Show::error('The swoole server worker process reload fail!');
            return;
        }

        output()->writef('<success>Tcp Server %s reload success</success>', $script);
    }

    /**
     * Stop the currently running server
     *
     * @CommandMapping()
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function stop(): void
    {
        $server = $this->createServer();

        // Check if it has started
        if (!$server->isRunning()) {
            output()->writeln('<error>The server is not running! cannot stop.</error>');
            return;
        }

        // Do stopping.
        $server->stop();
    }

    /**
     * Restart the tcp server
     *
     * @CommandMapping(usage="{fullCommand} [-d|--daemon]")
     * @CommandOption("daemon", short="d", desc="Run server on the background")
     *
     * @throws ReflectionException
     * @throws ContainerException
     * @example
     * {fullCommand}
     * {fullCommand} -d
     */
    public function restart(): void
    {
        $server = $this->createServer();

        // Check if it has started
        if ($server->isRunning()) {
            $success = $server->stop();

            if (!$success) {
                output()->error('Stop the old server failed!');
                return;
            }
        }

        // Restart server
        $server->startWithDaemonize();
    }

    /**
     * @return TcpServer
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function createServer(): TcpServer
    {
        $script  = input()->getScript();
        $command = $this->getFullCommand();

        /* @var TcpServer $server */
        $server = bean('tcpServer');
        $server->setScriptFile($script);
        $server->setFullCommand($command);

        return $server;
    }
}

