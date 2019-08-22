<?php declare(strict_types=1);

namespace SwoftTest\Http\Message\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\Request;

/**
 * Class RequestTest
 *
 * @package SwoftTest\Http\Message\Unit
 */
class RequestTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testBasic(): void
    {
        $sr = new \Swoole\Http\Request();
        $sr->fd = 1;
        $sr->server['request_uri'] = '/home/index';

        $r = Request::new($sr);

        $this->assertSame(1, $r->getFd());
        $this->assertSame('1.1', $r->getProtocolVersion());
        $this->assertSame('/home/index', $r->getUriPath());
        $this->assertSame('/home/index', $r->getUri()->getPath());

        $r->setUriPath('/about');
        $this->assertSame('/about', $r->getUriPath());
        $this->assertSame('/about', $r->getUri()->getPath());
    }
}
