<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Unit\Swoole;

use Swoft\Tcp\Server\Swoole\ConnectListener;
use SwoftTest\Tcp\Server\Unit\TcpServerTestCase;

/**
 * Class ConnectListenerTest
 */
class ConnectListenerTest extends TcpServerTestCase
{
    /**
     */
    public function testOnConnect(): void
    {
        // Ensure is empty
        // TempString::clear();

        $cl = new ConnectListener();
        // $cl->onConnect($this->swServer(), 1, 1);

        $this->assertNotEmpty($cl);
    }
}
