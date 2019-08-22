<?php declare(strict_types=1);


namespace Swoft\Task;


use Swoft;
use Swoft\Server\Server;
use Swoft\Task\Exception\TaskException;

class Task
{
    /**
     * Coroutine
     */
    public const CO = 'co';

    /**
     * Async
     */
    public const ASYNC = 'async';

    /**
     * @param string $name
     * @param string $method
     * @param array  $params
     * @param float  $timeout
     * @param array  $ext
     *
     * @return mixed
     * @throws TaskException
     */
    public static function co(string $name, string $method, array $params = [], float $timeout = 3, array $ext = [])
    {
        $tasks[] = [
            $name,
            $method,
            $params
        ];
        $result  = self::cos($tasks, $timeout, $ext);
        if (empty($result)) {
            throw new TaskException(
                sprintf('Task(name=%s method=%s) execution error!', $name, $method)
            );
        }
        return $result[0];
    }

    /**
     * @param string   $name
     * @param string   $method
     * @param array    $params
     * @param array    $ext
     * @param int      $dstWorkerId
     * @param callable $fallback
     *
     * @return string Task unique id
     * @throws TaskException
     */
    public static function async(
        string $name,
        string $method,
        array $params = [],
        array $ext = [],
        int $dstWorkerId = -1,
        callable $fallback = null
    ): string {
        $data   = Packet::pack(self::ASYNC, $name, $method, $params, $ext);
        $result = Swoft::swooleServer()->task($data, $dstWorkerId, $fallback);
        if ($result === false) {
            throw new TaskException(
                sprintf('Task error name=%d method=%d', $name, $method)
            );
        }

        return self::getUniqid($result);
    }

    /**
     * For example
     *
     * ```php
     * $tasks = [
     *     [
     *         'name',
     *         'method',
     *         []
     *     ],
     *     ...
     * ]
     * Task::cos($tasks);
     * ```
     *
     * @param array $tasks
     * @param float $timeout
     * @param array $ext
     *
     * @return array
     * @throws TaskException
     */
    public static function cos(array $tasks, float $timeout = 3, array $ext = []): array
    {
        $taskData = [];
        foreach ($tasks as $task) {
            if (count($task) < 3) {
                throw new TaskException('Task is bad format!');
            }

            [$name, $method, $params] = $task;
            if (!is_string($name) || !is_string($method) || !is_array($params)) {
                throw new TaskException('Task params is bad format!');
            }

            $taskData[] = Packet::pack(self::CO, $name, $method, $params, $ext);
        }

        $resultData  = [];
        $taskResults = Swoft::swooleServer()->taskCo($taskData, $timeout);
        foreach ($taskResults as $key => $taskResult) {
            if ($taskResult == false) {
                [$name, $method] = $tasks[$key];
                throw new TaskException(
                    sprintf('Task co error(name=%s method=%s)', $name, $method)
                );
            }

            [$result, $errorCode, $errorMessage] = Packet::unpackResponse($taskResult);
            if ($errorCode !== null) {
                throw new TaskException(
                    sprintf('%s(code=%d)', $errorMessage, $errorCode)
                );
            }
            $resultData[] = $result;
        }

        return $resultData;
    }

    /**
     * Get task global unique id
     *
     * @param int $taskId
     *
     * @return string
     */
    public static function getUniqid(int $taskId): string
    {
        $server = Server::getServer();
        if (empty($server)) {
            return sprintf('unit-%d', $taskId);
        }

        $serverUniqid = Server::getServer()->getUniqid();
        return sprintf('%s-%d', $serverUniqid, $taskId);
    }
}