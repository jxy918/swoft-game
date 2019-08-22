<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Unit;

use PHPUnit\Framework\TestCase;
use SwoftTest\WebSocket\Server\Testing\MockWsServer;

/**
 * Class WsServerTestCase
 *
 * @since 2.0
 */
abstract class WsServerTestCase extends TestCase
{
    /**
     * @var MockWsServer
     */
    protected $wsServer;

    public function setUp(): void
    {
        $this->wsServer = new MockWsServer();
    }
}
