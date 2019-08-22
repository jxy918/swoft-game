<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Unit;

use PHPUnit\Framework\TestCase;
use SwoftTest\Tcp\Server\Testing\MockTcpServer;
use Swoole\Server;

/**
 * Class TcpServerTestCase
 *
 * @since 2.0
 */
abstract class TcpServerTestCase extends TestCase
{
    /**
     * @var MockTcpServer
     */
    protected $tcpServer;

    public function setUp(): void
    {
        parent::setUp();

        $this->tcpServer = new MockTcpServer();
        $this->tcpServer->start();
    }

    public function swServer(): Server
    {
        return $this->tcpServer->getSwooleServer();
    }
}
