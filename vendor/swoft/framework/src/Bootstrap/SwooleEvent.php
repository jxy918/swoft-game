<?php

namespace Swoft\Bootstrap;

/**
 * the events of swoole
 *
 * @uses      SwooleEvent
 * @version   2018年01月11日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class SwooleEvent
{
    /**
     * the port of type
     */
    const TYPE_PORT = 'port';

    /**
     * the server of type
     */
    const TYPE_SERVER = 'server';

    /**
     * the event name of start
     */
    const ON_START = 'start';

    /**
     * the event name of workerStart
     */
    const ON_WORKER_START = 'workerStart';

    /**
     * the event name of managerStart
     */
    const ON_MANAGER_START = 'managerStart';

    /**
     * the event name of request
     */
    const ON_REQUEST = 'request';

    /**
     * the event name of task
     */
    const ON_TASK = 'task';

    /**
     * the event name of pipeMessage
     */
    const ON_PIPE_MESSAGE = 'pipeMessage';

    /**
     * the event name of finish
     */
    const ON_FINISH = 'finish';

    /**
     * the event name of connect
     */
    const ON_CONNECT = 'connect';

    /**
     * the event name of receive
     */
    const ON_RECEIVE = 'receive';

    /**
     * the event name of close
     */
    const ON_CLOSE = 'close';

    const ON_BEFORE_START = 'beforeStart';

    /**
     * for websocket
     */
    const ON_OPEN = 'open';
    const ON_HAND_SHAKE = 'handshake';
    const ON_MESSAGE = 'message';

    /**
     * @var array
     */
    private static $handlerFunctions = [
        self::ON_START         => 'onStart',
        self::ON_WORKER_START  => 'onWorkerStart',
        self::ON_MANAGER_START => 'onManagerStart',
        self::ON_REQUEST       => 'onRequest',
        self::ON_TASK          => 'onTask',
        self::ON_PIPE_MESSAGE  => 'onPipeMessage',
        self::ON_FINISH        => 'onFinish',
        self::ON_CONNECT       => 'onConnect',
        self::ON_RECEIVE       => 'onReceive',
        self::ON_CLOSE         => 'onClose',
        self::ON_BEFORE_START  => 'onBeforeStart',
        // for ws
        self::ON_OPEN  => 'onOpen',
        self::ON_MESSAGE  => 'onMessage',
        self::ON_HAND_SHAKE  => 'onHandshake',
    ];

    /**
     * get handler function of event
     *
     * @param string $event
     *
     * @return string
     */
    public static function getHandlerFunction(string $event): string
    {
        return self::$handlerFunctions[$event];
    }

    /**
     * @param string $event
     *
     * @return bool
     */
    public static function isSwooleEvent(string $event): bool
    {
        return isset(self::$handlerFunctions[$event]);
    }
}
