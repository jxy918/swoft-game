<?php

namespace Swoft\Rpc\Server\Router;

use Swoft\Http\Message\Router\HandlerMappingInterface;
use Swoft\Rpc\Server\Exception\RpcServerException;

/**
 * Handler of service
 */
class HandlerMapping implements HandlerMappingInterface
{
    /**
     * Service routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * Get handler from router
     *
     * @param array ...$params
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getHandler(...$params): array
    {
        list($interfaceClass, $version, $method) = $params;
        return $this->match($interfaceClass, $version, $method);
    }

    /**
     * Auto register routes
     *
     * @param array $serviceMapping
     */
    public function register(array $serviceMapping)
    {
        foreach ($serviceMapping as $interfaceName => $versions) {
            foreach ($versions as $version => $methods) {
                $this->registerRoute($interfaceName, $version, $methods);
            }
        }
    }

    /**
     * Match route
     *
     * @param $func
     *
     * @return array
     * @throws \InvalidArgumentException
     */

    /**
     * Match route
     *
     * @param string $interfaceClass
     * @param string $version
     * @param string $method
     *
     * @return array
     * @throws RpcServerException
     */
    public function match(string $interfaceClass, string $version, string $method): array
    {
        $serviceKey = $this->getServiceKey($interfaceClass, $version, $method);
        if (!isset($this->routes[$serviceKey])) {
            throw new RpcServerException(sprintf('The %s of %s %s is not exist! ', $method, $interfaceClass, $version));
        }

        return $this->routes[$serviceKey];
    }

    /**
     * Register one route
     *
     * @param string $interfaceName
     * @param string $version
     * @param array  $methods
     */
    private function registerRoute(string $interfaceName, string $version, array $methods)
    {
        foreach ($methods as $method => $handler) {
            $serviceKey                = $this->getServiceKey($interfaceName, $version, $method);
            $this->routes[$serviceKey] = $handler;
        }
    }

    /**
     * Get service key
     *
     * @param string $interfaceClass
     * @param string $version
     * @param string $method
     *
     * @return string
     */
    private function getServiceKey(string $interfaceClass, string $version, string $method): string
    {
        return \sprintf('%s_%s_%s', $interfaceClass, $version, $method);
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

}
