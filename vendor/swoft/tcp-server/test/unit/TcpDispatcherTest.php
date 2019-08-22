<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Unit;

use function bean;
use ReflectionException;
use Swoft\Tcp\Server\Exception\CommandNotFoundException;
use Swoft\Tcp\Server\Exception\TcpUnpackingException;
use Swoft\Tcp\Server\Request;
use SwoftTest\Tcp\Server\Testing\MockTcpResponse;

/**
 * Class TcpDispatcherTest
 */
class TcpDispatcherTest extends TcpServerTestCase
{
    /**
     * @throws ReflectionException
     * @throws CommandNotFoundException
     * @throws TcpUnpackingException
     */
    public function testDispatch(): void
    {
        $td = bean('tcpDispatcher');

        $req = Request::new(1, 'data', 2);
        $res = new MockTcpResponse();

        $this->assertNotEmpty($td);
        // $td->dispatch($req, $res);
    }
}
