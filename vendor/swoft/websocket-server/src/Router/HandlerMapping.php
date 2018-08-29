<?php

namespace Swoft\WebSocket\Server\Router;

use Swoft\Http\Message\Router\HandlerMappingInterface;


/**
 * Class HandlerMapping
 * @package Swoft\WebSocket\Server\Router
 */
class HandlerMapping implements HandlerMappingInterface
{
    const FOUND = 0;
    const NOT_FOUND = 1;

    /**
     * @var array
     * [
     *  '/echo' => [
     *      'handler' => handler,
     *      'option' => options
     *  ],
     *  ...
     * ]
     */
    protected $routes = [];

    /**
     * @param string $path
     * @param $handler
     * @param array $options
     */
    public function add(string $path, $handler, array $options = [])
    {
        $this->registerRoute($path, $handler, $options);
    }

    /**
     * Get handler from router
     *
     * @param array ...$params
     *
     * @return array
     * @throws \Swoft\WebSocket\Server\Exception\WsRouteException
     * @throws \InvalidArgumentException
     */
    public function getHandler(...$params): array
    {
        return $this->match($params[0]);
    }

    /**
     * Match route
     *
     * @param string $path e.g '/echo'
     * @return array
     * @throws \Swoft\WebSocket\Server\Exception\WsRouteException
     */
    public function match(string $path): array
    {
        $path = $path === '/' ? $path : \rtrim($path, '/ ');

        if (!isset($this->routes[$path])) {
            return [self::NOT_FOUND, $path];
        }

        return [self::FOUND, $this->routes[$path]];
    }

    /**
     * @param string $path
     * @return bool
     */
    public function hasRoute(string $path): bool
    {
        return isset($this->routes[$path]);
    }

    /**
     * Register one route
     *
     * @param string $path
     * @param mixed $handler
     * @param array $option
     */
    private function registerRoute(string $path, $handler, array $option = [])
    {
        $path = '/' . \trim($path, '/ ');

        $this->routes[$path] = [
            'handler' => $handler,
            'option' => $option
        ];
    }

    /**
     * Auto register routes
     *
     * @param array $serviceMapping
     */
    public function registerRoutes(array $serviceMapping)
    {
        foreach ($serviceMapping as $path => $value) {
            $this->registerRoute($path, $value['handler']);
        }
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }
}
