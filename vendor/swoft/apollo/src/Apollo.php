<?php declare(strict_types=1);


namespace Swoft\Apollo;

use Swoft\Apollo\Exception\ApolloException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoole\Coroutine\Http\Client;
use Throwable;

/**
 * Class Apollo
 *
 * @since 2.0
 *
 * @Bean("apollo")
 */
class Apollo
{
    /**
     * Pull success
     */
    public const SUCCESS = 200;

    /**
     * Not update config
     */
    public const NOT_MODIFIED = 304;

    /**
     * @var string
     */
    private $host = '127.0.0.1';

    /**
     * @var int
     */
    private $port = 8080;

    /**
     * @var string
     */
    private $appId = '';

    /**
     * @var string
     */
    private $clusterName = '';

    /**
     * Seconds
     *
     * @var int
     */
    private $timeout = 6;

    /**
     * @param string $uri
     * @param array  $options
     *
     * @param int    $timeout
     *
     * @return array
     * @throws ApolloException
     */
    public function request(string $uri, array $options, int $timeout): array
    {
        try {
            $query = $options['query'] ?? [];
            if (!empty($query)) {
                $query = http_build_query($query);
                $uri   = sprintf('%s?%s', $uri, $query);
            }

            // Request
            $client = new Client($this->host, $this->port);
            $client->set(['timeout' => $timeout]);
            $client->get($uri);
            $body   = $client->body;
            $status = $client->statusCode;
            $client->close();

            // Not update empty body
            if (!empty($body)) {
                $body = JsonHelper::decode($body, true);
            }

            if ($status == -1 || $status == -2 || $status == -3) {
                throw new ApolloException(
                    sprintf(
                        'Request timeout!(host=%s, port=%d timeout=%d)',
                        $this->host,
                        $this->port,
                        $this->timeout
                    )
                );
            }

            if ($status != self::SUCCESS && $status != self::NOT_MODIFIED) {
                $message = $body['message'] ?? '';
                throw new ApolloException(sprintf('Apollo server error is %s', $message));
            }
        } catch (Throwable $e) {
            throw new ApolloException(sprintf('Apollo(%s) pull fail!(%s)', $uri, $e->getMessage()));
        }

        // Not update return empty
        if ($status == self::NOT_MODIFIED) {
            return [];
        }

        return $body;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @return string
     */
    public function getClusterName(): string
    {
        return $this->clusterName;
    }
}