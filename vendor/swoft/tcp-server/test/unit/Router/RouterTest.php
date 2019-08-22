<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Unit\Router;

use function get_class;
use PHPUnit\Framework\TestCase;
use Swoft\Tcp\Server\Exception\TcpServerRouteException;
use Swoft\Tcp\Server\Router\Router;
use Throwable;

/**
 * Class RouterTest
 */
class RouterTest extends TestCase
{
    /**
     * @throws TcpServerRouteException
     */
    public function testBasic(): void
    {
        $r = new Router();

        $this->assertSame(0, $r->getCount());
        $this->assertSame('.', $r->getDelimiter());
        $this->assertSame('', $r->getDefaultCommand());

        $h1 = ['testHandler', 'method'];
        $r->add('test', $h1);

        [$status, $info] = $r->match('test');
        $this->assertSame(Router::FOUND, $status);
        $this->assertArrayHasKey('handler', $info);
        $this->assertSame($h1, $info['handler']);
        $this->assertArrayHasKey('command', $info);
        $this->assertSame('test', $info['command']);

        [$status,] = $r->match('not-exist');
        $this->assertSame(Router::NOT_FOUND, $status);

        $rs = $r->getRoutes();
        $this->assertArrayHasKey('test', $rs);

        $this->assertSame(1, $r->getCount());

        $r->setDelimiter(':');
        $this->assertSame(':', $r->getDelimiter());

        $r->setDefaultCommand('home.index');
        $this->assertSame('home.index', $r->getDefaultCommand());
    }

    public function testAddError(): void
    {
        $r = new Router();

        try {
            $r->add('', ['testHandler', 'method']);
        } catch (Throwable $e) {
            $this->assertSame(TcpServerRouteException::class, get_class($e));
            $this->assertSame('The tcp server route command cannot be empty', $e->getMessage());
        }

        try {
            $r->add('test', []);
        } catch (Throwable $e) {
            $this->assertSame(TcpServerRouteException::class, get_class($e));
            $this->assertSame('The tcp server command(test) handler cannot be empty',  $e->getMessage());
        }
    }
}
