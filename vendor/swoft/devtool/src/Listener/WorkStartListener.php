<?php declare(strict_types=1);

namespace Swoft\Devtool\Bootstrap\Listener;

use Swoft\Config\Annotation\Mapping\Config;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoole\Server;
use Swoft\Server\Swoole\SwooleEvent;

/**
 * Class WorkStartListener
 * @package Swoft\Devtool\Bootstrap\Listener
 * @Listener(SwooleEvent::WORKER_START)
 */
class WorkStartListener implements EventHandlerInterface
{
    /**
     * @Config("devtool.appLogToConsole")
     * @var bool
     */
    public $appLogToConsole = false;

    /**
     * @param Server $server
     * @param int    $workerId
     * @param bool   $isWorker
     * @throws \Throwable
     */
    public function onWorkerStart(Server $server, int $workerId, bool $isWorker)
    {
        if (!$enable = \config('devtool.enable', false)) {
            return;
        }

        \output()->writeln(\sprintf(
            'Children process start successful. ' .
            'PID <magenta>%s</magenta>, Worker Id <magenta>%s</magenta>, Role <info>%s</info>',
            $server->worker_pid,
            $workerId,
            $isWorker ? 'Worker' : 'Task'
        ));
    }

    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {
        // TODO: Implement handle() method.
    }
}
