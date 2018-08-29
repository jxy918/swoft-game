<?php

namespace Swoft\Task\Bootstrap\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\SwooleListener;
use Swoft\Bootstrap\Listeners\Interfaces\FinishInterface;
use Swoft\Bootstrap\Listeners\Interfaces\TaskInterface;
use Swoft\Bootstrap\SwooleEvent;
use Swoft\Core\Coroutine;
use Swoft\Event\AppEvent;
use Swoft\Task\Event\TaskEvent;
use Swoft\Task\TaskExecutor;
use Swoole\Server;

/**
 * The listener of swoole task
 * @SwooleListener({
 *     SwooleEvent::ON_TASK,
 *     SwooleEvent::ON_FINISH,
 * })
 */
class TaskEventListener implements TaskInterface, FinishInterface
{
    public function onFinish(Server $server, int $taskId, $data)
    {
        App::trigger(TaskEvent::FINISH_TASK, $taskId, $data);
    }

    /**
     * @param \Swoole\Server $server
     * @param int            $taskId
     * @param int            $workerId
     * @param mixed          $data
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function onTask(Server $server, int $taskId, int $workerId, $data)
    {
        try {
            /* @var TaskExecutor $taskExecutor*/
            $taskExecutor = App::getBean(TaskExecutor::class);
            $result = $taskExecutor->run($data);
        } catch (\Throwable $throwable) {
            App::error(sprintf('TaskExecutor->run %s file=%s line=%d ', $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()));
            $result = false;

            // Release system resources
            App::trigger(AppEvent::RESOURCE_RELEASE);

            App::trigger(TaskEvent::AFTER_TASK);
        }
        return $result;
    }
}