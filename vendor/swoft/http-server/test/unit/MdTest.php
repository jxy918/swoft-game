<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Unit;

use Swoft\Bean\Exception\ContainerException;
use SwoftTest\Http\Server\Testing\MockRequest;
use Swoft\Http\Message\ContentType;

/**
 * Class MdTest
 *
 * @since 2.0
 */
class MdTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function testMethod()
    {
        $headers = [
            'Method-md'      => 'ok',
            'Method-md2'     => 'ok',
            'Method-md3'     => 'ok',
            'Controller-md'  => 'ok',
            'Controller-md2' => 'ok',
            'Controller-md3' => 'ok',
            ContentType::KEY => ContentType::JSON
        ];

        $response = $this->mockServer->request(MockRequest::GET, '/testMw/method');
        $response->assertEqualJson(['method']);
        $headers[ContentType::KEY] = $response->getHeaderKey(ContentType::KEY);
        $response->assertEqualHeaders($headers);
    }

    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function testMethod2()
    {
        $headers = [
            'Method-md2'     => 'ok',
            'Controller-md'  => 'ok',
            'Controller-md2' => 'ok',
            'Controller-md3' => 'ok',
            ContentType::KEY => ContentType::JSON
        ];

        $response = $this->mockServer->request(MockRequest::GET, '/testMw/method2');
        $response->assertEqualJson(['method2']);
        $headers[ContentType::KEY] = $response->getHeaderKey(ContentType::KEY);
        $response->assertEqualHeaders($headers);
    }

    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function testMethod23()
    {
        $headers = [
            'Controller-md'  => 'ok',
            'Controller-md2' => 'ok',
            'Controller-md3' => 'ok',
            ContentType::KEY => ContentType::JSON
        ];

        $response = $this->mockServer->request(MockRequest::GET, '/testMw/method3');
        $response->assertEqualJson(['method3']);
        $headers[ContentType::KEY] = $response->getHeaderKey(ContentType::KEY);
        $response->assertEqualHeaders($headers);
    }
}
