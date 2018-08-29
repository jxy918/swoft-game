<?php

namespace Swoft\HttpClient;

use Psr\Http\Message\ResponseInterface;
use Swoft\Core\AbstractResult;
use Swoft\Http\Message\Stream\SwooleStream;
use Swoft\HttpClient\Adapter\ResponseTrait;
use Swoft\HttpClient\Exception\RuntimeException;

/**
 * Http Result
 */
class HttpResult extends AbstractResult implements HttpResultInterface
{
    use ResponseTrait;

    /**
     * @var resource
     */
    public $client;

    /**
     * Return result
     *
     * @param array $params
     * @return string
     * @throws \Swoft\HttpClient\Exception\RuntimeException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getResult(...$params): string
    {
        $response = $this->getResponse(...$params);
        return $response->getBody()->getContents();
    }

    /**
     * @alias getResult()
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Swoft\HttpClient\Exception\RuntimeException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getResponse(...$params): ResponseInterface
    {
        $client = $this->client;
        if (!\is_resource($client)) {
            throw new RuntimeException('Supplied resource is not a valid cURL handler resource');
        }

        $status = curl_getinfo($client, CURLINFO_HTTP_CODE);
        $headers = curl_getinfo($client);
        curl_close($client);
        $response = $this->createResponse()
                         ->withBody(new SwooleStream($this->result ?? ''))
                         ->withStatus($status)
                         ->withHeaders($headers ?? []);

        return $response;
    }

}
