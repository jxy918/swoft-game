<?php declare(strict_types=1);

namespace Swoft\Task\Swoole;

use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Server\Contract\FinishInterface;
use Swoft\Task\TaskEvent;
use Swoole\Coroutine;
use Swoole\Server;

/**
 * Class FinishListener
 *
 * @since 2.0
 *
 * @Bean()
 */
class FinishListener implements FinishInterface
{
    /**
     * @param Server $server
     * @param int    $taskId
     * @param string $data
     */
    public function onFinish(Server $server, int $taskId, string $data): void
    {
        Coroutine::create(function () use ($server, $taskId, $data) {
            // Before finish
            Swoft::trigger(TaskEvent::BEFORE_FINISH, null, $server, $taskId, $data);

            // Do finish
            Swoft::trigger(TaskEvent::FINISH);

            // After finish
            Swoft::trigger(TaskEvent::AFTER_FINISH);
        });
    }
}
