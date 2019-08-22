<?php declare(strict_types=1);


namespace Swoft\Apollo\Contract;

use ReflectionException;
use Swoft\Apollo\Exception\ApolloException;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class ConfigInterface
 *
 * @since 2.0
 */
interface ConfigInterface
{
    /**
     * Pull config with cache
     *
     * @param string $namespace
     * @param string $clientIp
     *
     * @return array
     * @throws ApolloException
     */
    public function pullWithCache(string $namespace, string $clientIp = ''): array;

    /**
     * @param string $namespace
     * @param string $releaseKey
     *
     * @param string $clientIp
     *
     * @return array
     */
    public function pull(string $namespace, string $releaseKey = '', string $clientIp = ''): array;

    /**
     * @param array  $namespaces
     * @param string $clientIp
     *
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function batchPull(array $namespaces, string $clientIp = ''): array;

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
    public function listen(array $namespaces, $callback, array $notifications = [], string $clientIp = ''): void;
}