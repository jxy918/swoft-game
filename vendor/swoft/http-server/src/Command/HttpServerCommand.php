<?php declare(strict_types=1);

namespace Swoft\Http\Server\Command;

use ReflectionException;
use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Helper\Show;
use Swoft\Http\Server\HttpServer;
use Swoft\Server\Command\BaseServerCommand;
use Swoft\Server\Exception\ServerException;
use function bean;
use function input;
use function output;

/**
 * Provide some commands to manage the swoft HTTP server
 *
 * @since 2.0
 *
 * @Command("http", alias="httpsrv", coroutine=false)
 * @example
 *  {fullCmd}:start     Start the http server
 *  {fullCmd}:stop      Stop the http server
 */
class HttpServerCommand extends BaseServerCommand
{
    /**
     * Start the http server
     *
     * @CommandMapping(usage="{fullCommand} [-d|--daemon]")
     * @CommandOption("daemon", short="d", desc="Run server on the background", type="bool", default="false")
     *
     * @throws ReflectionException
     * @throws ContainerException
     * @throws ServerException
     * @example
     *   {fullCommand}
     *   {fullCommand} -d
     *
     */
    public function start(): void
    {
        $server = $this->createServer();

        // Check if it has started
        if ($server->isRunning()) {
            $masterPid = $server->getPid();
            output()->writeln("<error>The HTTP server have been running!(PID: {$masterPid})</error>");
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
        $modeName = $server->getModeName();
        $typeName = $server->getTypeName();

        // Http
        $panel = [
            'HTTP' => [
                'listen' => $mainHost . ':' . $mainPort,
                'type'   => $typeName,
                'mode'   => $modeName,
                'worker' => $workerNum,
            ],
        ];

        // Port Listeners
        $panel = $this->appendPortsToPanel($server, $panel);

        Show::panel($panel);

        output()->writeln('<success>HTTP server start success !</success>');

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
            output()->writeln('<error>The HTTP server is not running! cannot reload</error>');
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

        output()->writef('<success>HTTP server %s reload success</success>', $script);
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
            output()->writeln('<error>The HTTP server is not running! cannot stop.</error>');
            return;
        }

        // Do stopping.
        $server->stop();
    }

    /**
     * Restart the http server
     *
     * @CommandMapping(usage="{fullCommand} [-d|--daemon]",)
     * @CommandOption("daemon", short="d", desc="Run server on the background")
     *
     * @throws ReflectionException
     * @throws ContainerException
     * @example
     *  {fullCommand}
     *  {fullCommand} -d
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

        output()->writef('<success>Server HTTP restart success !</success>');
        $server->startWithDaemonize();
    }

    /**
     * @return HttpServer
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function createServer(): HttpServer
    {
        $script  = input()->getScript();
        $command = $this->getFullCommand();

        /** @var HttpServer $server */
        $server = bean('httpServer');
        $server->setScriptFile(Swoft::app()->getPath($script));
        $server->setFullCommand($command);

        return $server;
    }
}
