<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Swoft\Bean\Exception\ContainerException;
use Swoft\WebSocket\Server\Message\MiddlewareChain;
use Swoft\WebSocket\Server\Message\Request;
use SwoftTest\WebSocket\Server\Testing\CoreMiddleware;
use SwoftTest\WebSocket\Server\Testing\User1Middleware;
use SwoftTest\WebSocket\Server\Testing\User2Middleware;
use Swoole\WebSocket\Frame;

/**
 * Class MiddlewareChainTest
 */
class MiddlewareChainTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testRun(): void
    {
        $coreMdl = new CoreMiddleware();

        $mc = MiddlewareChain::new($coreMdl);
        $mc->middle(new User1Middleware());
        $mc->add(new User2Middleware());

        $req  = Request::new(new Frame());
        $resp = $mc->run($req);

        $this->assertSame(100, $resp->getSender());
        $this->assertSame('>user1 >user2 [CORE] user2> user1>', $resp->getData());
    }
}
