<?php declare(strict_types=1);


namespace SwoftTest\Task\Testing;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Task\Exception\TaskException;
use Swoft\Task\Packet;
use Swoft\Task\Request;
use Swoft\Task\Response;
use Swoft\Task\Task;
use Swoft\Task\TaskDispatcher;
use Swoole\Server;
use Swoole\Server\Task as SwooleTask;


/**
 * Class MockTaskServer
 *
 * @since 2.0
 *
 * @Bean()
 */
class MockTaskServer
{
    /**
     * @param string $name
     * @param string $method
     * @param array  $params
     * @param array  $ext
     *
     * @return string
     * @throws ContainerException
     * @throws TaskException
     * @throws ReflectionException
     */
    public function co(string $name, string $method, array $params = [], array $ext = [])
    {
        $server     = null;
        $task       = new SwooleTask();
        $task->id   = 1;
        $task->data = Packet::pack(Task::CO, $name, $method, $params, $ext);

        $request = MockRequest::new($server, $task);

        $response = MockResponse::new($task);
        $this->task($request, $response);

        $taskResult = $response->getResponseData();
        [$result, $errorCode, $errorMessage] = Packet::unpackResponse($taskResult);
        if ($errorCode !== null) {
            throw new TaskException(
                sprintf('%s(code=%d)', $errorMessage, $errorCode)
            );
        }

        return $result;
    }

    /**
     * @param string $name
     * @param string $method
     * @param array  $params
     * @param array  $ext
     *
     * @return int
     * @throws ContainerException
     * @throws TaskException
     * @throws ReflectionException
     */
    public function async(
        string $name,
        string $method,
        array $params = [],
        array $ext = []
    ): int {
        $server = new Server('127.0.0.1');
        $task   = new SwooleTask();

        $task->data = Packet::pack(Task::ASYNC, $name, $method, $params, $ext);
        $request    = MockRequest::new($server, $task);

        $response = MockResponse::new($task);
        $this->task($request, $response);

        return mt_rand(1, 100);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @throws ContainerException
     * @throws ReflectionException
     */
    private function task(Request $request, Response $response)
    {
        /* @var TaskDispatcher $dispatcher */
        $dispatcher = BeanFactory::getBean('taskDispatcher');
        $dispatcher->dispatch($request, $response);
    }
}