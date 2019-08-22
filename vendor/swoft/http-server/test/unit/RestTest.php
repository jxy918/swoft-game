<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Unit;


use Swoft\Http\Message\ContentType;
use Swoft\Stdlib\Helper\JsonHelper;
use SwoftTest\Http\Server\Testing\MockRequest;

/**
 * Class RestTest
 *
 * @since 2.0
 */
class RestTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testList()
    {
        $headers = [
            ContentType::KEY => ContentType::JSON
        ];

        $response = $this->mockServer->request(MockRequest::GET, '/testRestUser', [], $headers, []);
        $response->assertEqualJson(['list']);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testCreate()
    {
        $headers = [
            ContentType::KEY => ContentType::JSON
        ];

        $data = [
            'name' => 'swoft',
            'desc' => 'swoft framework'
        ];

        $ext      = [
            'content' => JsonHelper::encode($data)
        ];
        $response = $this->mockServer->request(MockRequest::POST, '/testRest/user', [], $headers, [], $ext);
        $response->assertEqualJson($data);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testGetUser()
    {
        $headers = [
            ContentType::KEY => ContentType::JSON
        ];

        $data = [
            'getUser',
            126
        ];

        $response = $this->mockServer->request(MockRequest::GET, '/testRest/126', [], $headers, []);
        $response->assertEqualJson($data);


        $response = $this->mockServer->request(MockRequest::GET, '/testRest/notInit', [], $headers, []);
        $response->assertEqualJson(['getUser', 0]);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testGetBookFromUser()
    {
        $headers = [
            ContentType::KEY => ContentType::JSON
        ];

        $data = [
            'bookFromUser',
            126,
            129
        ];

        $response = $this->mockServer->request(MockRequest::GET, '/testRest/126/book/129', [], $headers, []);
        $response->assertEqualJson($data);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testDelete()
    {
        $headers = [
            ContentType::KEY => ContentType::JSON
        ];

        $data = [
            'delete',
            126
        ];

        $response = $this->mockServer->request(MockRequest::DELETE, '/testRest/126', [], $headers);
        $response->assertEqualJson($data);
    }

    /**
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function testUpdate()
    {
        $headers = [
            ContentType::KEY => ContentType::JSON
        ];

        $data = [
            'name' => 'swoft',
            'desc' => 'swoft framework'
        ];

        $ext = [
            'content' => JsonHelper::encode($data)
        ];

        $response = $this->mockServer->request(MockRequest::PUT, '/testRest/126', [], $headers, [], $ext);

        $data['update'] = 'update';
        $data['uid']    = 126;
        $response->assertEqualJson($data);
    }
}