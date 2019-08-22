<?php declare(strict_types=1);


namespace Swoft\Task\Swoole;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Server\Contract\TaskInterface;
use Swoft\Task\Exception\TaskException;
use Swoft\Task\Request;
use Swoft\Task\Response;
use Swoft\Task\TaskDispatcher;
use Swoole\Server;
use Swoole\Server\Task as SwooleTask;

/**
 * Class TaskListener
 *
 * @since 2.0
 *
 * @Bean()
 */
class TaskListener implements TaskInterface
{
    /**
     * @param Server     $server
     * @param SwooleTask $task
     *
     * @throws ReflectionException
     * @throws ContainerException
     * @throws TaskException
     */
    public function onTask(Server $server, SwooleTask $task): void
    {
        $request  = Request::new($server, $task);
        $response = Response::new($task);

        /* @var TaskDispatcher $dispatcher */
        $dispatcher = BeanFactory::getBean('taskDispatcher');
        $dispatcher->dispatch($request, $response);
    }
}