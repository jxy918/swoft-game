<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Unit;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Tcp\Server\TcpServer;
use function bean;
use const SWOOLE_PROCESS;
use const SWOOLE_SOCK_TCP;

/**
 * Class TcpServerTest
 */
class TcpServerTest extends TcpServerTestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testBasic(): void
    {
        // $swServer = $this->tcpServer->getSwooleServer();
        /** @var TcpServer $tcpSrv */
        $tcpSrv = bean('tcpServer');

        $this->assertSame(18309, $tcpSrv->getPort());
        $this->assertSame('0.0.0.0', $tcpSrv->getHost());
        $this->assertSame(SWOOLE_PROCESS, $tcpSrv->getMode());
        $this->assertSame(SWOOLE_SOCK_TCP, $tcpSrv->getType());
        $this->assertSame('TCP', $tcpSrv->getServerType());
        $this->assertSame('Process', $tcpSrv->getModeName());
    }
}
