<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Server\Exception\ServerException;
use Swoft\Server\Server;
use Swoft\Server\SwooleEvent;
use Swoole\Websocket\Frame;
use Throwable;
use function array_flip;
use function array_shift;
use function count;
use function end;
use const WEBSOCKET_OPCODE_TEXT;

/**
 * Class WebSocketServer
 *
 * @Bean("wsServer")
 *
 * @since 2.0
 */
class WebSocketServer extends Server
{
    /**
     * @var string
     */
    protected static $serverType = 'WebSocket';

    /**
     * Default listen port
     *
     * @var int
     */
    protected $port = 18308;

    /**
     * @var string
     */
    protected $pidName = 'swoft-ws';

    /**
     * @var string
     */
    protected $pidFile = '@runtime/swoft-ws.pid';

    /**
     * @var string
     */
    protected $commandFile = '@runtime/swoft-ws.command';

    /**
     * Start swoole server
     *
     * @throws ServerException
     * @throws Throwable
     */
    public function start(): void
    {
        $this->swooleServer = new \Swoole\Websocket\Server($this->host, $this->port, $this->mode, $this->type);

        $this->startSwoole();
    }

    /*****************************************************************************
     * helper methods for send message
     ****************************************************************************/

    /**
     * Send data to client by frame object.
     * NOTICE: require swoole version >= 4.2.0
     *
     * @param Frame $frame
     *
     * @return bool
     */
    public function pushFrame(Frame $frame): bool
    {
        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpStrictTypeCheckingInspection */
        return $this->swooleServer->push($frame);
    }

    /**
     * @param int    $fd
     * @param string $data   Data for send to client. NOTICE: max size is 2M.
     * @param int    $opcode WebSocket opcode value
     *                       text:   WEBSOCKET_OPCODE_TEXT   = 1
     *                       binary: WEBSOCKET_OPCODE_BINARY = 2
     *                       close:  WEBSOCKET_OPCODE_CLOSE  = 8
     *                       ping:   WEBSOCKET_OPCODE_PING   = 9
     *                       pong:   WEBSOCKET_OPCODE_PONG   = 10
     * @param bool   $finish
     *
     * @return bool
     */
    public function push(int $fd, string $data, int $opcode = WEBSOCKET_OPCODE_TEXT, bool $finish = true): bool
    {
        return $this->sendTo($fd, $data, 0, $opcode, $finish);
    }

    /**
     * Send a message to the specified user
     *
     * @param int    $receiver The receiver fd
     * @param string $data
     * @param int    $sender   The sender fd
     * @param int    $opcode
     * @param bool   $finish
     *
     * @return bool
     */
    public function sendTo(
        int $receiver, string $data, int $sender = 0, int $opcode = WEBSOCKET_OPCODE_TEXT, bool $finish = true
    ): bool {
        if (!$this->swooleServer->isEstablished($receiver)) {
            return false;
        }

        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;
        $this->log("(private)The #{$fromUser} send message to the user #{$receiver}. Data: {$data}");

        return $this->swooleServer->push($receiver, $data, $opcode, $finish);
    }

    /**
     * Send message to client(s)
     *
     * @param string|Frame $data
     * @param int|array $receivers
     * @param int|array $excluded
     * @param int       $sender
     * @param int       $pageSize
     * @param int       $opcode
     *
     * @return int Return send count
     */
    public function send(
        $data,
        $receivers = 0,
        $excluded = 0,
        int $sender = 0,
        int $pageSize = 50,
        int $opcode = WEBSOCKET_OPCODE_TEXT
    ): int
    {
        if (!$data) {
            return 0;
        }

        $receivers = (array)$receivers;
        $excluded  = (array)$excluded;

        // Only one receiver
        if (1 === count($receivers)) {
            $ok = $this->sendTo((int)array_shift($receivers), $data, $sender, $opcode);
            return $ok ? 1 : 0;
        }

        // To all
        if (!$excluded && !$receivers) {
            return $this->sendToAll($data, $sender, $pageSize, $opcode);
        }

        // To some
        return $this->sendToSome($data, $receivers, $excluded, $sender, $pageSize, $opcode);
    }

    /**
     * Broadcast message to all user, but will exclude sender
     *
     * @param string $data      Message data
     * @param int[]  $receivers Designated receivers(FD list)
     * @param int[]  $excluded  The receivers to be excluded
     * @param int    $sender    Sender FD
     * @param int    $opcode
     *
     * @return int Return send count
     */
    public function broadcast(
        string $data,
        array $receivers = [],
        array $excluded = [],
        int $sender = 0,
        int $opcode = WEBSOCKET_OPCODE_TEXT
    ): int
    {
        // if (!$data) {
        //     return 0;
        // }

        // Only one receiver
        if (1 === count($receivers)) {
            $ok = $this->sendTo((int)array_shift($receivers), $data, $sender, $opcode);
            return $ok ? 1 : 0;
        }

        // Excepted itself
        if ($sender) {
            $excluded[] = $sender;
        }

        // To all
        if (!$excluded && !$receivers) {
            return $this->sendToAll($data, $sender, 50, $opcode);
        }

        // To some
        return $this->sendToSome($data, $receivers, $excluded, $sender, 50, $opcode);
    }

    /**
     * Send message to all connections
     *
     * @param string|Frame $data
     * @param int          $sender
     * @param int          $pageSize
     * @param int          $opcode
     *
     * @return int
     */
    public function sendToAll($data, int $sender = 0, int $pageSize = 50, int $opcode = WEBSOCKET_OPCODE_TEXT): int
    {
        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;
        $this->log("(broadcast)The #{$fromUser} send a message to all users. Data: {$data}");

        return $this->pageEach(function (int $fd) use ($data, $opcode) {
            $this->swooleServer->push($fd, $data, $opcode);
        }, $pageSize);
    }

    /**
     * @param string|Frame $data
     * @param array  $receivers
     * @param array  $excluded
     * @param int    $sender
     * @param int    $pageSize
     * @param int    $opcode
     *
     * @return int
     */
    public function sendToSome(
        $data,
        array $receivers = [],
        array $excluded = [],
        int $sender = 0,
        int $pageSize = 50,
        int $opcode = WEBSOCKET_OPCODE_TEXT
    ): int {
        $count    = 0;
        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;

        // To receivers
        if ($receivers) {
            $this->log("(broadcast)The #{$fromUser} gave some specified user sending a message. Data: {$data}");

            foreach ($receivers as $fd) {
                if ($this->swooleServer->isEstablished($fd)) {
                    $count++;
                    $this->swooleServer->push($fd, $data, $opcode);
                }
            }

            return $count;
        }

        // To special users
        $excluded = $excluded ? (array)array_flip($excluded) : [];

        $this->log("(broadcast)The #{$fromUser} send the message to everyone except some people. Data: {$data}");

        return $this->pageEach(function (int $fd) use ($excluded, $data, $opcode) {
            if (isset($excluded[$fd])) {
                return;
            }

            $this->swooleServer->push($fd, $data, $opcode);
        }, $pageSize);
    }

    /*****************************************************************************
     * helper methods
     ****************************************************************************/

    /**
     * Traverse all valid WS connections FD
     *
     * @param callable $handler
     *
     * @return int
     */
    public function each(callable $handler): int
    {
        $count = 0;
        foreach ($this->swooleServer->connections as $fd) {
            if ($this->swooleServer->isEstablished($fd)) {
                $count++;
                $handler($fd);
            }
        }

        return $count;
    }

    /**
     * Pagination traverse all valid WS connection
     *
     * @param callable $handler
     * @param int      $pageSize
     *
     * @return int
     */
    public function pageEach(callable $handler, int $pageSize = 50): int
    {
        $count = $startFd = 0;

        while (true) {
            $fdList = (array)$this->swooleServer->getClientList($startFd, $pageSize);
            if (($num = count($fdList)) === 0) {
                break;
            }

            $count += $num;

            /** @var $fdList array */
            foreach ($fdList as $fd) {
                if ($this->swooleServer->isEstablished($fd)) {
                    $handler($fd);
                }
            }

            // It's last page.
            if ($num < $pageSize) {
                break;
            }

            // Get start fd for next page.
            $startFd = end($fdList);
        }

        return $count;
    }

    /**
     * Disconnect for client, will trigger onClose
     *
     * @param int    $fd
     * @param int    $code
     * @param string $reason
     *
     * @return bool
     */
    public function disconnect(int $fd, int $code = 0, string $reason = ''): bool
    {
        if ($this->swooleServer->isEstablished($fd)) {
            return $this->swooleServer->disconnect($fd, $code, $reason);
        }

        return true;
    }

    /**
     * Check it is valid websocket connection(has been handshake)
     *
     * @param int $fd
     *
     * @return bool
     */
    public function isEstablished(int $fd): bool
    {
        return $this->swooleServer->isEstablished($fd);
    }

    /**
     * @return bool
     */
    public function httpIsEnabled(): bool
    {
        return isset($this->on[SwooleEvent::REQUEST]);
    }
}
