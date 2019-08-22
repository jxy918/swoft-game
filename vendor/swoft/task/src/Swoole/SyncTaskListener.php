<?php declare(strict_types=1);


namespace Swoft\Task\Swoole;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Server\Contract\SyncTaskInterface;
use Swoft\Task\Exception\TaskException;
use Swoft\Task\Packet;
use Swoft\Task\SyncTaskDispatcher;
use Swoole\Server;

/**
 * Class SyncTaskListener
 *
 * @since 2.0
 *
 * @Bean()
 */
class SyncTaskListener implements SyncTaskInterface
{
    /**
     * @Inject()
     *
     * @var SyncTaskDispatcher
     */
    private $dispatcher;

    /**
     * Dispatch
     *
     * @param Server $server
     * @param int    $taskId
     * @param int    $srcWorkerId
     * @param mixed  $data
     *
     * @return mixed
     * @throws TaskException
     */
    public function onTask(Server $server, $taskId, int $srcWorkerId, $data)
    {
        // Task params
        [$type, $name, $method, $params, $ext] = Packet::unpack($data);

        return $this->dispatcher->dispatch($type, $name, $method, $params, $ext);
    }
}