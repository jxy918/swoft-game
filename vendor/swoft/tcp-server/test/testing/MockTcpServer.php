<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Testing;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Tcp\Server\Exception\TcpResponseException;
use Swoft\Tcp\Server\Swoole\ConnectListener;
use Swoft\Tcp\Server\Swoole\ReceiveListener;
use Swoft\Tcp\Server\TcpServer;
use Swoole\Server;
use function bean;

/**
 * Class MockTcpServer
 */
class MockTcpServer
{
    /**
     * @var array [fd => 1]
     */
    private $fds = [];

    /**
     * @var int
     */
    private $counter = 1;

    /**
     * @var TcpServer
     */
    private $tcpServer;

    /**
     * @var Server
     */
    private $swServer;

    public function start(): void
    {
        $this->tcpServer = new TcpServer();
        // $this->swServer  = new Server($ts->getHost(), $ts->getPort(), $ts->getMode(), $ts->getType());
    }

    /**
     * @return Server
     */
    public function getSwooleServer(): Server
    {
        return $this->swServer;
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function mockConnect(): void
    {
        $fd = $this->counter++;
        $cl = bean(ConnectListener::class);

        // Save fd
        $this->fds[$fd] = 1;

        $cl->onConnect($this->swooleServer, $fd, $fd);
    }

    /**
     * @param string $data
     *
     * @throws ContainerException
     * @throws ReflectionException
     * @throws TcpResponseException
     */
    public function mockReceive(string $data): void
    {
        $fd = $this->counter;
        $rl = bean(ReceiveListener::class);

        $rl->onReceive($this->swooleServer, $fd, $fd, $data);
    }
}
