<?php declare(strict_types=1);


namespace Swoft\Apollo;


use ReflectionException;
use Swoft\Apollo\Contract\ConfigInterface;
use Swoft\Apollo\Exception\ApolloException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Co;
use Swoft\Stdlib\Helper\JsonHelper;
use Swoft\Stdlib\Helper\PhpHelper;

/**
 * Class Config
 *
 * @since 2.0
 *
 * @Bean()
 */
class Config implements ConfigInterface
{
    /**
     * @Inject()
     *
     * @var Apollo
     */
    private $apollo;

    /**
     * Pull config with cache
     *
     * @param string $namespace
     * @param string $clientIp
     *
     * @return array
     * @throws ApolloException
     */
    public function pullWithCache(string $namespace, string $clientIp = ''): array
    {
        $appid       = $this->apollo->getAppId();
        $clusterName = $this->apollo->getClusterName();
        $timeout     = $this->apollo->getTimeout();

        if (empty($clientIp)) {
            $clientIp = $this->getClientIp();
        }

        $options = [
            'query' => [
                'clientIp' => $clientIp
            ]
        ];

        $uri = sprintf('/configfiles/json/%s/%s/%s', $appid, $clusterName, $namespace);
        return $this->apollo->request($uri, $options, $timeout);
    }

    /**
     * @param string $namespace
     * @param string $releaseKey
     *
     * @param string $clientIp
     *
     * @return array
     * @throws ApolloException
     */
    public function pull(string $namespace, string $releaseKey = '', string $clientIp = ''): array
    {
        $appid       = $this->apollo->getAppId();
        $clusterName = $this->apollo->getClusterName();
        $timeout     = $this->apollo->getTimeout();

        if (empty($clientIp)) {
            $clientIp = $this->getClientIp();
        }

        // Client ip and release key
        $query['clientIp'] = $clientIp;
        if (!empty($releaseKey)) {
            $query['releaseKey'] = $releaseKey;
        }

        $options = [
            'query' => $query
        ];

        $uri = sprintf('/configs/%s/%s/%s', $appid, $clusterName, $namespace);
        return $this->apollo->request($uri, $options, $timeout);
    }

    /**
     * @param array  $namespaces
     * @param string $clientIp
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function batchPull(array $namespaces, string $clientIp = ''): array
    {
        $requests = [];
        foreach ($namespaces as $namespace) {
            $requests[$namespace] = function () use ($namespace, $clientIp) {
                return $this->pull($namespace, '', $clientIp);
            };
        }

        return Co::multi($requests, $this->apollo->getTimeout());
    }

    /**
     * @param array          $namespaces
     * @param callable|array $callback
     * @param array          $notifications
     * @param string         $clientIp
     *
     * @throws ApolloException
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function listen(array $namespaces, $callback, array $notifications = [], string $clientIp = ''): void
    {
        $appid       = $this->apollo->getAppId();
        $clusterName = $this->apollo->getClusterName();

        // Client ip and release key
        $query['appId']   = $appid;
        $query['cluster'] = $clusterName;

        // Init $notifications
        if (empty($notifications)) {
            foreach ($namespaces as $namespace) {
                $notifications[$namespace] = [
                    'namespaceName'  => $namespace,
                    'notificationId' => -1
                ];
            }
        }

        // Long polling
        while (true) {
            $updateNamespaceNames   = [];
            $query['notifications'] = JsonHelper::encode(array_values($notifications));

            $options = [
                'query' => $query
            ];

            $result = $this->apollo->request('/notifications/v2', $options, -1);
            if (empty($result)) {
                continue;
            }

            foreach ($result as $nsNotification) {
                $namespaceName  = $nsNotification['namespaceName'];
                $notificationId = $nsNotification['notificationId'];

                // Update notifications
                $notifications[$namespaceName] = [
                    'namespaceName'  => $namespaceName,
                    'notificationId' => $notificationId
                ];

                $updateNamespaceNames[] = $namespaceName;
            }

            $updateConfigs = $this->batchPull($updateNamespaceNames, $clientIp);
            PhpHelper::call($callback, $updateConfigs);
        }
    }

    /**
     * @return string
     */
    private function getClientIp(): string
    {
        $clientIp = swoole_get_local_ip();
        return $clientIp['eth0'] ?? '127.0.0.1';
    }
}