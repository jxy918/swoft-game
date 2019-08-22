<?php declare(strict_types=1);

namespace SwoftTest\Http\Server\Unit;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\ContentType;
use SwoftTest\Http\Server\Testing\Controller\TestController;
use SwoftTest\Http\Server\Testing\MockRequest;

/**
 * Class ResponseTest
 *
 * @package SwoftTest\Http\Server\Unit
 */
class ResponseTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testCookie(): void
    {
        /** @see TestController */
        $response = $this->mockServer->request(MockRequest::GET, '/fixture/test/cookie');

        $this->assertNotEmpty($cks = $response->getCookie());
        $this->assertArrayHasKey('ck', $cks);
        $this->assertSame('ck=val', $cks['ck']);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testHtml(): void
    {
        /** @see TestController */
        $response = $this->mockServer->request(MockRequest::GET, '/fixture/test/htmlData');

        $this->assertNotEmpty($c = $response->getContent());
        $this->assertNotEmpty($hs = $response->getHeaders());
        $this->assertArrayHasKey(ContentType::KEY, $hs);
        $this->assertSame('text/html; charset=utf-8', $hs[ContentType::KEY]);
        $this->assertSame('<h1>hello</h1>', $c);

        /** @see TestController */
        $response = $this->mockServer->request(MockRequest::GET, '/fixture/test/htmlContent');

        $this->assertNotEmpty($c = $response->getContent());
        $this->assertNotEmpty($hs = $response->getHeaders());
        $this->assertArrayHasKey(ContentType::KEY, $hs);
        $this->assertSame('text/html; charset=utf-8', $hs[ContentType::KEY]);
        $this->assertSame('<h1>hello</h1>', $c);
    }
}
