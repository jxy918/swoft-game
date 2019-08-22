<?php declare(strict_types=1);


namespace Swoft\Redis\Connector;

use Redis;
use RedisCluster;
use RedisClusterException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Redis\Contract\ConnectorInterface;
use Swoft\Redis\Exception\RedisException;
use Swoft\Stdlib\Helper\Arr;
use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Class PhpRedisConnector
 *
 * @since 2.0
 *
 * @Bean()
 */
class PhpRedisConnector implements ConnectorInterface
{
    /**
     * @param array $config
     * @param array $option
     *
     * @return Redis
     * @throws RedisException
     */
    public function connect(array $config, array $option): Redis
    {
        $client = new Redis();
        $this->establishConnection($client, $config);

        if (!empty($config['password'])) {
            $client->auth($config['password']);
        }

        if (!empty($config['database'])) {
            $client->select($config['database']);
        }

        if (!empty($config['read_timeout'])) {
            $client->setOption(Redis::OPT_READ_TIMEOUT, (string)$config['read_timeout']);
        }

        if (!empty($option['prefix'])) {
            $client->setOption(Redis::OPT_PREFIX, $option['prefix']);
        }

        if (!empty($option['serializer'])) {
            $client->setOption(Redis::OPT_SERIALIZER, (string)$option['serializer']);
        }

        return $client;
    }

    /**
     * @param array $config
     * @param array $option
     *
     * @return RedisCluster
     * @throws RedisClusterException
     */
    public function connectToCluster(array $config, array $option): RedisCluster
    {
        $servers     = array_map([$this, 'buildClusterConnectionString'], $config);
        $servers     = array_values($servers);
        $readTimeout = $option['read_timeout'] ?? 0;
        $timeout     = $option['timeout'] ?? 0;
        $persistent  = $option['persistent'] ?? false;

        $redisCluster = new RedisCluster(null, $servers, $timeout, $readTimeout, $persistent);
        return $redisCluster;
    }

    /**
     * Build a single cluster seed string from array.
     *
     * @param array $server
     *
     * @return string
     */
    protected function buildClusterConnectionString(array $server): string
    {
        $allowParams = [
            'database',
            'password',
            'prefix',
            'read_timeout',
        ];

        $params = Arr::only($server, $allowParams);
        $query  = Arr::query($params);
        return $server['host'] . ':' . $server['port'] . '?' . $query;
    }

    /**
     * Establish a connection with the Redis host.
     *
     * @param Redis $client
     * @param array $config
     *
     * @return void
     * @throws RedisException
     */
    protected function establishConnection(Redis $client, array $config): void
    {
        $parameters = [
            $config['host'],
            $config['port'],
            $config['timeout'],
            '',
            $config['retry_interval'],
        ];

        if (version_compare(phpversion('redis'), '3.1.3', '>=')) {
            $parameters[] = $config['read_timeout'];
        }

        $result = $client->connect(...$parameters);
        if ($result === false) {
            throw new RedisException(
                sprintf('Redis connect error(%s)', JsonHelper::encode($parameters, JSON_UNESCAPED_UNICODE))
            );
        }
    }
}
