<?php declare(strict_types=1);


namespace Swoft\Process\Command;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Console\Annotation\Mapping\Command;
use Swoft\Console\Annotation\Mapping\CommandMapping;
use Swoft\Console\Annotation\Mapping\CommandOption;
use Swoft\Console\Helper\Show;
use Swoft\Process\Exception\ProcessException;
use Swoft\Process\Process;
use Swoft\Process\ProcessPool;
use Swoft\Server\Command\BaseServerCommand;

/**
 * Class ProcessCommand
 *
 * @since 2.0
 *
 * @Command(name="process", coroutine=false)
 * @example
 *  {fullCmd}:start     Start the process pool
 *  {fullCmd}:stop      Stop the process pool
 */
class ProcessCommand extends BaseServerCommand
{
    /**
     * @CommandMapping(usage="{fullCommand} [-d|--daemon]")
     * @CommandOption("daemon", short="d", desc="Run server on the background", type="bool", default="false")
     *
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ProcessException
     * @example
     *   {fullCommand}
     *   {fullCommand} -d
     */
    public function start(): void
    {
        $server = $this->createServer();

        // Check if it has started
        if ($server->isRunning()) {
            $masterPid = $server->getPid();
            output()->writeln("<error>The Process pool have been running!(PID: {$masterPid})</error>");
            return;
        }

        // Daemon
        $asDaemon = input()->getSameOpt(['d', 'daemon'], false);
        if ($asDaemon) {
            $server->setDaemonize();
        }

        $server->start();
    }

    /**
     * @CommandMapping()
     *
     * @throws ContainerException
     * @throws ProcessException
     * @throws ReflectionException
     */
    public function restart(): void
    {
        $server = $this->createServer();

        // Check if it has started
        if ($server->isRunning()) {
            $success = $server->stop();
            if (!$success) {
                output()->error('Stop the old process pool failed!');
                return;
            }
        }

        output()->writef('<success>Process pool restart success !</success>');

        $server->setDaemonize();
        $server->start();
    }

    /**
     * @CommandMapping()
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function reload(): void
    {
        $server = $this->createServer();
        $script = input()->getScript();

        // Check if it has started
        if (!$server->isRunning()) {
            output()->writeln('<error>The Process pool is not running! cannot reload</error>');
            return;
        }

        output()->writef('<info>Server %s is reloading</info>', $script);

        if (!$server->reload()) {
            Show::error('The process pool worker process reload fail!');
            return;
        }

        output()->writef('<success>Process pool %s reload success</success>', $script);
    }

    /**
     * @CommandMapping()
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function stop(): void
    {
        $server = $this->createServer();

        // Check if it has started
        if (!$server->isRunning()) {
            output()->writeln('<error>The Process pool is not running! cannot stop.</error>');
            return;
        }

        // Do stopping.
        $server->stop();
    }

    /**
     * @return ProcessPool
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function createServer(): ProcessPool
    {
        $script  = input()->getScript();
        $command = $this->getFullCommand();

        /** @var ProcessPool $processPool */
        $processPool = bean('processPool');
        $processPool->setScriptFile(\Swoft::app()->getPath($script));
        $processPool->setFullCommand($command);

        return $processPool;
    }
}