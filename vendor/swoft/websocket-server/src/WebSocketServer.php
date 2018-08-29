<?php

namespace Swoft\WebSocket\Server;

use Swoft\Bootstrap\SwooleEvent;
use Swoft\Console\Helper\ConsoleUtil;
use Swoft\Http\Server\Http\HttpServer;
use Swoole\WebSocket\Server;

/**
 * Class WebSocketServer
 * @package Swoft\WebSocket\Server
 * @author inhere <in.798@qq.com>
 * @property \Swoole\WebSocket\Server $server
 */
class WebSocketServer extends HttpServer
{
    use WebSocketEventTrait;

    /**
     * @var array
     */
    private $wsSettings = [
        // enable handler http request ?
        'enable_http' => true,
    ];

    /**
     * @param array $settings
     * @throws \InvalidArgumentException
     */
    public function initSettings(array $settings)
    {
        parent::initSettings($settings);

        $this->wsSettings = \array_merge($this->httpSetting, $this->wsSettings, $settings['ws']);
    }

    /**
     *
     * @throws \Swoft\Exception\RuntimeException
     */
    public function start()
    {
        // add server type
        $this->serverSetting['server_type'] = self::TYPE_WS;

        if (!empty($this->setting['open_http2_protocol'])) {
            $this->wsSettings['type'] = SWOOLE_SOCK_TCP | SWOOLE_SSL;
        }

        $this->server = new Server(
            $this->wsSettings['host'],
            $this->wsSettings['port'],
            $this->wsSettings['mode'],
            $this->wsSettings['type']
        );

        // config server
        $this->server->set($this->setting);

        // Bind event callback
        $this->server->on(SwooleEvent::ON_START, [$this, 'onStart']);
        $this->server->on(SwooleEvent::ON_WORKER_START, [$this, 'onWorkerStart']);
        $this->server->on(SwooleEvent::ON_MANAGER_START, [$this, 'onManagerStart']);
        $this->server->on(SwooleEvent::ON_PIPE_MESSAGE, [$this, 'onPipeMessage']);

        // bind events for ws server
        $this->server->on(SwooleEvent::ON_HAND_SHAKE, [$this, 'onHandshake']);
        // NOTICE: The onOpen event is not fired after an onHandShake callback function
        // $this->server->on(SwooleEvent::ON_OPEN, [$this, 'onOpen']);
        $this->server->on(SwooleEvent::ON_MESSAGE, [$this, 'onMessage']);
        $this->server->on(SwooleEvent::ON_CLOSE, [$this, 'onClose']);

        // if enable handle http request
        if ($this->wsSettings['enable_http']) {
            $this->server->on(SwooleEvent::ON_REQUEST, [$this, 'onRequest']);
        }

        // Start RPC Server
        if ((int)$this->serverSetting['tcpable'] === 1) {
            $this->registerRpcEvent();
        }

        $this->registerSwooleServerEvents();
        $this->beforeServerStart();

        // start
        $this->server->start();
    }

    /**
     * @param string $msg
     * @param array $data
     * @param string $type
     */
    public function log(string $msg, array $data = [], string $type = 'info')
    {
        if ($this->isDaemonize()) {
            return;
        }

        if (\config('debug')) {
            ConsoleUtil::log($msg, $data, $type);
        }
    }

    /*****************************************************************************
     * some methods for send message
     ****************************************************************************/

    /**
     * @param string $fd
     * @param string $data
     * @param bool $isBinary
     * @param bool $finish
     * @return bool
     */
    public function push(string $fd, string $data, $isBinary = false, bool $finish = true): bool
    {
        if (!$this->server->exist($fd)) {
            return false;
        }

        return $this->server->push($fd, $data, $isBinary, $finish);
    }

    /**
     * send message to client(s)
     * @param string $data
     * @param int|array $receivers
     * @param int|array $excluded
     * @param int $sender
     * @param int $pageSize
     * @return int
     */
    public function send(string $data, $receivers = 0, $excluded = 0, int $sender = 0, int $pageSize = 50): int
    {
        if (!$data) {
            return 0;
        }

        $receivers = (array)$receivers;
        $excluded = (array)$excluded;

        // only one receiver
        if (1 === \count($receivers)) {
            return $this->sendTo((int)\array_shift($receivers), $data, $sender);
        }

        // to all
        if (!$excluded && !$receivers) {
            return $this->sendToAll($data, $sender, $pageSize);
        }

        // to some
        return $this->sendToSome($data, $receivers, $excluded, $sender, $pageSize);
    }

    /**
     * Send a message to the specified user 发送消息给指定的用户
     * @param int $receiver 接收者 fd
     * @param string $data
     * @param int $sender 发送者 fd
     * @return int
     */
    public function sendTo(int $receiver, string $data, int $sender = 0): int
    {
        $finish = true;
        $opcode = 1;
        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;

        $this->log("(private)The #{$fromUser} send message to the user #{$receiver}. Data: {$data}");

        return $this->server->push($receiver, $data, $opcode, $finish) ? 1 : 0;
    }

    /**
     * broadcast message, will exclude sender.
     * @param string $data 消息数据
     * @param int $sender 发送者
     * @param int[] $receivers 指定接收者们
     * @param int[] $excluded 要排除的接收者
     * @return int Return socket last error number code.  gt 0 on failure, eq 0 on success
     */
    public function broadcast(string $data, array $receivers = [], array $excluded = [], int $sender = 0): int
    {
        if (!$data) {
            return 0;
        }

        // only one receiver
        if (1 === \count($receivers)) {
            return $this->sendTo((int)\array_shift($receivers), $data, $sender);
        }

        // excepted itself
        if ($sender) {
            $excluded[] = $sender;
        }

        // to all
        if (!$excluded && !$receivers) {
            return $this->sendToAll($data, $sender);
        }

        // to some
        return $this->sendToSome($data, $receivers, $excluded, $sender);
    }

    /**
     * send message to all connections
     * @param string $data
     * @param int $sender
     * @param int $pageSize
     * @return int
     */
    public function sendToAll(string $data, int $sender = 0, int $pageSize = 50): int
    {
        $startFd = 0;
        $count = 0;
        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;
        $this->log("(broadcast)The #{$fromUser} send a message to all users. Data: {$data}");

        while (true) {
            $fdList = $this->server->connection_list($startFd, $pageSize);

            if ($fdList === false || ($num = \count($fdList)) === 0) {
                break;
            }

            $count += $num;
            $startFd = \end($fdList);

            /** @var $fdList array */
            foreach ($fdList as $fd) {
                $info = $this->getClientInfo($fd);

                if ($info && $info['websocket_status'] > 0) {
                    $this->server->push($fd, $data);
                }
            }
        }

        return $count;
    }

    /**
     * @param string $data
     * @param array $receivers
     * @param array $excluded
     * @param int $sender
     * @param int $pageSize
     * @return int
     */
    public function sendToSome(string $data, array $receivers = [], array $excluded = [], int $sender = 0, int $pageSize = 50): int
    {
        $count = 0;
        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;

        // to receivers
        if ($receivers) {
            $this->log("(broadcast)The #{$fromUser} gave some specified user sending a message. Data: {$data}");

            foreach ($receivers as $receiver) {
                if ($this->exist($receiver)) {
                    $count++;
                    $this->server->push($receiver, $data);
                }
            }

            return $count;
        }

        // to special users
        $startFd = 0;
        $excluded = $excluded ? (array)\array_flip($excluded) : [];

        $this->log("(broadcast)The #{$fromUser} send the message to everyone except some people. Data: {$data}");

        while (true) {
            $fdList = $this->server->connection_list($startFd, $pageSize);

            if ($fdList === false || ($num = \count($fdList)) === 0) {
                break;
            }

            $count += $num;
            $startFd = \end($fdList);

            /** @var $fdList array */
            foreach ($fdList as $fd) {
                if (isset($excluded[$fd])) {
                    continue;
                }

                $this->server->push($fd, $data);
            }
        }

        return $count;
    }

    /*****************************************************************************
     * helper methods
     ****************************************************************************/

    /**
     * response data to client by socket connection
     * @param int $fd
     * @param string $data
     * param int $length
     * @return int   Return error number code. gt 0 on failure, eq 0 on success
     */
    public function writeTo($fd, string $data): int
    {
        return $this->server->send($fd, $data) ? 0 : 1;
    }

    /**
     * @param int $fd
     * @return bool
     */
    public function exist(int $fd): bool
    {
        return $this->server->exist($fd);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->server->connections);
    }

    /**
     * @return int
     */
    public function getErrorNo(): int
    {
        return $this->server->getLastError();
    }

    /**
     * @param int $fd
     * @return array
     */
    public function getClientInfo(int $fd): array
    {
        return $this->server->getClientInfo($fd);
    }

    /**
     * @return array
     */
    public function getWsSettings(): array
    {
        return $this->wsSettings;
    }
}
