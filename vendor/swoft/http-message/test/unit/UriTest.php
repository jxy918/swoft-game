<?php declare(strict_types=1);

namespace SwoftTest\Http\Message\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\Uri\Uri;

/**
 * Class UriTest
 */
class UriTest extends TestCase
{
    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testBasic(): void
    {
        $uri = Uri::new('/home/index.html');
        $this->assertNotEmpty($uri);
        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('/home/index.html', $uri->getPath());
        $this->assertSame('', $uri->getAuthority());

        $uri = Uri::new('', [
            'path' => '/home/index'
        ]);

        $this->assertNotEmpty($uri);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testIssue792(): void
    {
        $uri = Uri::new('http://ic.clive.domain.com/db');

        $this->assertSame('ic.clive.domain.com', $uri->getHost());

        $this->assertNull($uri->getPort());
        $this->assertSame('ic.clive.domain.com', $uri->getHost());
        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('/db', $uri->getPath());
        $this->assertSame('ic.clive.domain.com', $uri->getAuthority());
    }
}
