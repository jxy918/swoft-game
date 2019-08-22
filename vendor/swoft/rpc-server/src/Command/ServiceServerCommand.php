<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Command;


use function bean;
use function input;
use function output;
use ReflectionException;
use Swoft;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Helper\Show;
use Swoft\Rpc\Server\ServiceServer;
use Swoft\Server\Command\BaseServerCommand;
use Swoft\Server\Exception\ServerException;
use Swoft\Server\ServerInterface;

/**
 * Class ServiceServerCommand
 *
 * @since 2.0
 *
 * @Command("rpc", coroutine=false, desc="Provide some commands to manage swoft RPC server")
 *
 * @example
 *  {fullCmd}:start     Start the rpc server
 *  {fullCmd}:stop      Stop the rpc server
 */
class ServiceServerCommand extends BaseServerCommand
{
    /**
     * Start the http server
     *
     * @CommandMapping(usage="{fullCommand} [-d|--daemon]")
     * @CommandOption("daemon", short="d", desc="Run server on the background")
     *
     * @example
     *  {fullCommand}
     *  {fullCommand} -d
     *
     * @throws ReflectionException
     * @throws ContainerException
     * @throws ServerException
     */
    public function start(): void
    {
        $server = $this->createServer();

        // Check if it has started
        if ($server->isRunning()) {
            $masterPid = $server->getPid();
            output()->writeln("<error>The RPC server have been running!(PID: {$masterPid})</error>");
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

        // RPC
        $panel = [
            'RPC' => [
                'listen' => $mainHost . ':' . $mainPort,
                'type'   => $typeName,
                'mode'   => $modeName,
                'worker' => $workerNum,
            ],
        ];

        // Listener
        $listeners = $server->getListener();
        foreach ($listeners as $name => $listener) {
            if (!$listener instanceof ServerInterface) {
                continue;
            }
            $panel[$name] = [
                'listen' => sprintf('%s:%s', $listener->getHost(), $listener->getPort()),
                'type'   => $listener->getTypeName()
            ];
        }

        Show::panel($panel);

        output()->writef('<success>RPC server start success !</success>');

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
            output()->writeln('<error>The RPC server is not running! cannot reload</error>');
            return;
        }

        output()->writef('<info>RPC server %s is reloading</info>', $script);

        if ($reloadTask = input()->hasOpt('t')) {
            Show::notice('Will only reload task worker');
        }

        if (!$server->reload($reloadTask)) {
            Show::error('The swoole server worker process reload fail!');
            return;
        }

        output()->writef('<success>RPC server %s reload success</success>', $script);
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
            output()->writeln('<error>The RPC server is not running! cannot stop.</error>');
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
     * @example
     *  {fullCommand}
     *  {fullCommand} -d
     * @throws ReflectionException
     * @throws ContainerException
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

        output()->writef('<success>RPC server reload success !</success>');
        $server->startWithDaemonize();
    }

    /**
     * @return ServiceServer
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function createServer(): ServiceServer
    {
        $script = input()->getScript();
        $command = $this->getFullCommand();

        /** @var ServiceServer $server */
        $server = bean('rpcServer');
        $server->setScriptFile(Swoft::app()->getPath($script));
        $server->setFullCommand($command);

        return $server;
    }
}
