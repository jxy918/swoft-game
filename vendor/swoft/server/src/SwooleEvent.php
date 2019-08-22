<?php declare(strict_types=1);


namespace Swoft\Server;

use Swoft\Server\Contract\CloseInterface;
use Swoft\Server\Contract\ConnectInterface;
use Swoft\Server\Contract\FinishInterface;
use Swoft\Server\Contract\HandshakeInterface;
use Swoft\Server\Contract\MessageInterface;
use Swoft\Server\Contract\PacketInterface;
use Swoft\Server\Contract\PipeMessageInterface;
use Swoft\Server\Contract\ReceiveInterface;
use Swoft\Server\Contract\RequestInterface;
use Swoft\Server\Contract\SyncTaskInterface;
use Swoft\Server\Contract\TaskInterface;

/**
 * Class SwooleEvent
 *
 * @since 2.0
 */
class SwooleEvent
{
    /**
     * Start
     */
    public const START = 'start';

    /**
     * Shutdown
     */
    public const SHUTDOWN = 'shutdown';

    /**
     * WorkerStart
     */
    public const WORKER_START = 'workerStart';

    /**
     * WorkerStop
     */
    public const WORKER_STOP = 'workerStop';

    /**
     * WorkerError
     */
    public const WORKER_ERROR = 'workerError';

    /**
     * ManagerStart
     */
    public const MANAGER_START = 'managerStart';

    /**
     * ManagerStop
     */
    public const MANAGER_STOP = 'managerStop';

    /**
     * Task
     */
    public const TASK = 'task';

    /**
     * Finish
     */
    public const FINISH = 'finish';

    /**
     * PipeMessage
     */
    public const PIPE_MESSAGE = 'pipeMessage';

    /**
     * Handshake
     */
    public const HANDSHAKE = 'handshake';

    /**
     * Message
     */
    public const MESSAGE = 'message';

    /**
     * Open
     */
    public const OPEN = 'open';

    /**
     * Request
     */
    public const REQUEST = 'request';

    /**
     * Packet
     */
    public const PACKET = 'packet';

    /**
     * Receive
     */
    public const RECEIVE = 'receive';

    /**
     * Connect
     */
    public const CONNECT = 'connect';

    /**
     * Close
     */
    public const CLOSE = 'close';

    /**
     * Event interface listener mapping
     */
    public const LISTENER_MAPPING = [
        // For http server
        self::REQUEST      => RequestInterface::class,
        // For websocket server
        self::HANDSHAKE    => HandshakeInterface::class,
        self::MESSAGE      => MessageInterface::class,
        // For tcp server
        self::CLOSE        => CloseInterface::class,
        self::RECEIVE      => ReceiveInterface::class,
        self::CONNECT      => ConnectInterface::class,
        // For udp server
        self::PACKET       => PacketInterface::class,
        // For task
        self::TASK         => [
            SyncTaskInterface::class,
            TaskInterface::class
        ],
        self::FINISH       => FinishInterface::class,
        // For process
        self::PIPE_MESSAGE => PipeMessageInterface::class,
    ];
}