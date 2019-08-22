<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Testing;

use ReflectionException;
use RuntimeException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\Request as ServerRequest;
use Swoft\Http\Message\Response as ServerResponse;
use Swoft\Http\Server\HttpDispatcher;
use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Class MockHttpServer
 *
 * @since 2.0
 *
 * @Bean()
 */
class MockHttpServer
{
    /**
     * @param string $method
     * @param string $uri
     * @param array  $params
     * @param array  $headers
     * @param array  $cookies
     * @param array  $ext
     *
     * @return MockResponse
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function request(
        string $method,
        string $uri,
        array $params = [],
        array $headers = [],
        array $cookies = [],
        array $ext = []
    ): MockResponse {
        $request = $this->mockRequest($method, $uri, $params, $headers, $cookies, $ext);

        $response = $this->onRequest($request, new MockResponse);
        $response = $response->getCoResponse();

        if (!$response instanceof MockResponse) {
            throw new RuntimeException('Mock request return is not MockResponse');
        }

        return $response;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $params
     * @param array  $headers
     * @param array  $cookies
     * @param array  $ext
     *
     * @return Request
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function mockRequest(
        string $method,
        string $uri,
        array $params = [],
        array $headers = [],
        array $cookies = [],
        array $ext = []
    ): Request {
        $servers = [
            'request_method' => $method,
            'request_uri'    => $uri,
            'path_info'      => $uri,
        ];

        $request = MockRequest::new($servers, $headers, $cookies, $params);

        $content = $ext['content'] ?? '';
        $request->setContent($content);

        return $request;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return ServerResponse
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function onRequest(Request $request, Response $response): ServerResponse
    {
        $psrRequest  = ServerRequest::new($request);
        $psrResponse = ServerResponse::new($response);

        /* @var HttpDispatcher $dispatcher */
        $dispatcher = BeanFactory::getSingleton('httpDispatcher');

        $dispatcher->dispatch($psrRequest, $psrResponse);

        return $psrResponse;
    }
}
