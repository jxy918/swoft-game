<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Unit;

use function bean;

/**
 * Class WsDispatcherTest
 *
 * @since 2.0
 */
class WsDispatcherTest extends WsServerTestCase
{
    public function testHandshake(): void
    {
        $dp = bean('wsDispatcher');

        $this->assertNotEmpty($dp);
    }
}
