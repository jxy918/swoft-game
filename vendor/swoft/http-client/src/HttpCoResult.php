<?php

namespace Swoft\HttpClient;

use Psr\Http\Message\ResponseInterface;
use Swoft\Core\AbstractResult;
use Swoft\Http\Message\Stream\SwooleStream;
use Swoft\HttpClient\Adapter\ResponseTrait;


/**
 * Http Defer Result
 *
 * @property \Swoole\Http\Client|resource $connection
 */
class HttpCoResult extends AbstractResult implements HttpResultInterface
{

    use ResponseTrait;

    /**
     * Return result
     *
     * @param array $params
     * @return string
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
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function getResponse(...$params): ResponseInterface
    {
        $client = $this->connection;
        $this->recv();
        $result = $client->body;
        $client->close();
        $headers = value(function () {
            $headers = [];
            foreach ($this->connection->headers as $key => $value) {
                $exploded = explode('-', $key);
                foreach ($exploded as &$str) {
                    $str = ucfirst($str);
                }
                $ucKey = implode('-', $exploded);
                $headers[$ucKey] = $value;
            }
            unset($str);
            return $headers;
        });
        $response = $this->createResponse()
                         ->withBody(new SwooleStream($result ?? ''))
                         ->withHeaders($headers ?? [])
                         ->withStatus($this->deduceStatusCode($client));
        return $response;
    }

    /**
     * Transfer sockets error code to HTTP status code.
     * TODO transfer more error code
     *
     * @param \Swoole\HttpClient $client
     * @return int
     */
    private function deduceStatusCode($client): int
    {
        if ($client->errCode === 110) {
            $status = 404;
        } else {
            $status = $client->statusCode;
        }
        return $status > 0 ? $status : 500;
    }

}