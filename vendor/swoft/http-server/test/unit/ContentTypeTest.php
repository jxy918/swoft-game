<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Unit;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\ContentType;

/**
 * Class ContentTypeTest
 *
 * @since 2.0
 */
class ContentTypeTest extends TestCase
{
    public function testUserCt()
    {
        $headers = [
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
        ];

        $response = $this->mockServer->request('POST', '/ct/userCt', [], $headers);

        $response->assertEqualHeader(ContentType::KEY, $response->getHeaderKey(ContentType::KEY));
        $response->assertEqualContent('imag data content');
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testUserCt2(): void
    {
        $headers = [
            'accept' => ContentType::XML,
        ];

        $response = $this->mockServer->request('POST', '/ct/userCt2', [], $headers);

        $response->assertEqualHeader(ContentType::KEY, $response->getHeaderKey(ContentType::KEY));
        $response->assertEqualContent('xml data content');
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function testUserCt3(): void
    {
        $headers = [
            'accept' => ContentType::XML,
        ];

        $response = $this->mockServer->request('POST', '/ct/userCt3', [], $headers);

        $response->assertEqualHeader(ContentType::KEY, $response->getHeaderKey(ContentType::KEY));
        $response->assertEqualContent('<xml><key><![CDATA[data]]></key></xml>');

        $headers = [];

        $response = $this->mockServer->request('POST', '/ct/userCt3', [], $headers);

        $response->assertEqualHeader(ContentType::KEY, $response->getHeaderKey(ContentType::KEY));
        $response->assertEqualJson(['key' => 'data']);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function testCtm(): void
    {
        $headers = [
            'accept'       => ContentType::JSON,
            'content-type' => 'application/json;charset=utf-8',
        ];

        $data     = [
            'key' => 'value'
        ];
        $response = $this->mockServer->request('GET', '/ct/ctm', $data, $headers);
        $response->assertEqualJson([$data, $data]);
    }
}
