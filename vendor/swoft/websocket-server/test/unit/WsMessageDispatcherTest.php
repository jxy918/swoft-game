<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Class WsMessageDispatcherTest
 */
class WsMessageDispatcherTest extends TestCase
{
    /**
     */
    public function testBasic(): void
    {
        $wmd = bean('wsMsgDispatcher');
        $this->assertNotEmpty($wmd);
    }

    public function testDispatch(): void
    {
        $wmd = bean('wsMsgDispatcher');
        $this->assertNotEmpty($wmd);
    }

    /**
     */
    public function testCustomDispatch(): void
    {
        $wmd = bean('wsMsgDispatcher');
        $this->assertNotEmpty($wmd);
    }
}
