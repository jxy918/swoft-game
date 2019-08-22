<?php declare(strict_types=1);


namespace Swoft\Consul;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Consul\Exception\ClientException;
use Swoft\Consul\Exception\ConsulException;
use Swoft\Consul\Exception\ServerException;
use Swoft\Log\Helper\Log;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoole\Coroutine\Http\Client;
use Throwable;

/**
 * Class Consul
 *
 * @since 2.0
 *
 * @Bean("consul")
 */
class Consul
{
    /**
     * @var string
     */
    private $host = '127.0.0.1';

    /**
     * @var int
     */
    private $port = 8500;

    /**
     * Seconds
     *
     * @var int
     */
    private $timeout = 3;

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function get(string $url = null, array $options = []): Response
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function head(string $url, array $options = []): Response
    {
        return $this->request('HEAD', $url, $options);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function delete(string $url, array $options = []): Response
    {
        return $this->request('DELETE', $url, $options);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function put(string $url, array $options = []): Response
    {
        return $this->request('PUT', $url, $options);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function patch(string $url, array $options = []): Response
    {
        return $this->request('PATCH', $url, $options);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function post(string $url, array $options = []): Response
    {
        return $this->request('POST', $url, $options);
    }

    /**
     * @param string $url
     * @param array  $options
     *
     * @return Response
     * @throws ClientException
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ServerException
     */
    public function options(string $url, array $options = []): Response
    {
        return $this->request('OPTIONS', $url, $options);
    }

    /**
     * @param $method
     * @param $uri
     * @param $options
     *
     * @return Response
     * @throws ClientException
     * @throws ServerException
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function request($method, $uri, $options): Response
    {
        $body = $options['body'] ?? '';
        if (is_array($body)) {
            $body = JsonHelper::encode($body, JSON_UNESCAPED_UNICODE);
        }

        $query = $options['query'] ?? [];
        if (!empty($query)) {
            $query = http_build_query($query);
            $uri   = sprintf('%s?%s', $uri, $query);
        }

        Log::debug('Requesting %s %s %s', $method, $uri, JsonHelper::encode($options));

        try {
            Log::profileStart($uri);

            // Http request
            $client = new Client($this->host, $this->port);
            $client->setMethod($method);
            $client->set(['timeout' => $this->timeout]);

            // Set body
            if (!empty($body)) {
                $client->setData($body);
            }

            $client->execute($uri);

            // Response
            $headers    = $client->headers;
            $statusCode = $client->statusCode;
            $body       = $client->body;

            // Close
            $client->close();

            Log::profileEnd($uri);

            if ($statusCode == -1 || $statusCode == -2 || $statusCode == -3) {
                throw new ConsulException(
                    sprintf(
                        'Request timeout!(host=%s, port=%d timeout=%d)',
                        $this->host,
                        $this->port,
                        $this->timeout
                    )
                );
            }

        } catch (Throwable $e) {
            $message = sprintf('Consul is fail! (uri=%s status=%s body=%s).', $uri, $e->getMessage(), $body);
            Log::error($message);
            throw new ServerException($message);
        }

        if (400 <= $statusCode) {
            $message = sprintf('Consul is fail! (uri=%s status=%s  body=%s)', $uri, $statusCode, $body);
            if (500 <= $statusCode) {
                Log::error($message);
                throw new ServerException($message, $statusCode);
            }

            Log::error($message);
            throw new ClientException($message, $statusCode);
        }

        return Response::new($headers, $body, $statusCode);
    }
}