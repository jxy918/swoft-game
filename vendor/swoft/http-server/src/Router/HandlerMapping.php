<?php

namespace Swoft\Http\Server\Router;

use Swoft\Http\Message\Router\HandlerMappingInterface;

/**
 * handler mapping of http
 *
 * @uses      HandlerMapping
 * @version   2017年07月14日
 * @author    inhere <in.798@qq.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 *
 * @method get(string $route, mixed $handler, array $opts = [])
 * @method post(string $route, mixed $handler, array $opts = [])
 * @method put(string $route, mixed $handler, array $opts = [])
 * @method delete(string $route, mixed $handler, array $opts = [])
 * @method options(string $route, mixed $handler, array $opts = [])
 * @method head(string $route, mixed $handler, array $opts = [])
 * @method search(string $route, mixed $handler, array $opts = [])
 * @method trace(string $route, mixed $handler, array $opts = [])
 * @method any(string $route, mixed $handler, array $opts = [])
 */
class HandlerMapping extends AbstractRouter implements HandlerMappingInterface
{
    /** @var int */
    private $cacheCounter = 0;

    /** @var int */
    protected $routeCounter = 0;

    /**
     * The param route cache number.
     * @var int
     */
    public $tmpCacheNumber = 200;

    /**
     * There are last route caches. like static routes
     * @var array[]
     * [
     *     '/user/login' => [
     *          // METHOD => INFO [...]
     *          'GET' => [
     *              'handler' => 'handler0',
     *              'option' => [...],
     *          ],
     *          'PUT' => [
     *              'handler' => 'handler1',
     *              'option' => [...],
     *          ],
     *          ...
     *      ],
     *      ... ...
     * ]
     */
    protected $cacheRoutes = [];

    /** @var array global Options */
    private $globalOptions = [
        // 'domains' => [ 'localhost' ], // allowed domains
        // 'schemas' => [ 'http' ], // allowed schemas
        // 'time' => ['12'],
    ];

    /*******************************************************************************
     * route collection
     ******************************************************************************/

    /**
     * @param string|array $methods The match request method(s).
     * e.g
     *  string: 'get'
     *  array: ['get','post']
     * @param string $route The route path string. is allow empty string. eg: '/user/login'
     * @param callable|string $handler
     * @param array $opts some option data
     * [
     *     'params' => [ 'id' => '[0-9]+', ],
     *     'defaults' => [ 'id' => 10, ],
     *     'domains'  => [ 'a-domain.com', '*.b-domain.com'],
     *     'schemas' => ['https'],
     * ]
     * @return static
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function map($methods, string $route, $handler, array $opts = []): AbstractRouter
    {
        $methods = $this->validateArguments($methods, $handler);
        list($route, $conf) = $this->prepareForMap($route, $handler, $opts);

        // it is static route
        if (self::isStaticRoute($route)) {
            foreach ($methods as $method) {
                if ($method === 'ANY') {
                    continue;
                }

                $this->routeCounter++;
                $this->staticRoutes[$route][$method] = $conf;
            }

            return $this;
        }

        // collect param route
        $this->collectParamRoute($route, $methods, $conf);

        return $this;
    }

    /**
     * @param string $route
     * @param $handler
     * @param array $opts
     * @return array
     */
    protected function prepareForMap(string $route, $handler, array $opts): array
    {
        if (!$this->initialized) {
            $this->initialized = true;
        }

        $hasPrefix = (bool)$this->currentGroupPrefix;

        // always add '/' prefix.
        if ($route = \trim($route)) {
            $route = $route{0} === '/' ? $route : '/' . $route;
        } elseif (!$hasPrefix) {
            $route = '/';
        }

        $route = $this->currentGroupPrefix . $route;

        // setting 'ignoreLastSlash'
        if ($route !== '/' && $this->ignoreLastSlash) {
            $route = \rtrim($route, '/');
        }

        $conf = [
            'handler' => $handler,
        ];

        if ($this->currentGroupOption) {
            $opts = \array_merge($this->currentGroupOption, $opts);
        }

        if ($opts) {
            $conf['option'] = $opts;
        }

        return [$route, $conf];
    }

    /**
     * @param string $route
     * @param array $methods
     * @param array $conf
     * @throws \LogicException
     */
    protected function collectParamRoute(string $route, array $methods, array $conf)
    {
        $conf['original'] = $route;
        $params = $this->getAvailableParams($conf['option']['params'] ?? []);
        list($first, $conf) = $this->parseParamRoute($route, $params, $conf);

        // route string have regular
        if ($first) {
            $conf['methods'] = \implode(',', $methods) . ',';
            $this->routeCounter++;
            $this->regularRoutes[$first][] = $conf;

            return;
        }

        foreach ($methods as $method) {
            if ($method === 'ANY') {
                continue;
            }

            $this->routeCounter++;
            $this->vagueRoutes[$method][] = $conf;
        }
    }

    /*******************************************************************************
     * route match
     ******************************************************************************/

    /**
     * find the matched route info for the given request uri path
     * @param string $method
     * @param string $path
     * @return array
     */
    public function match(string $path, string $method = 'GET'): array
    {
        // if enable 'matchAll'
        if ($matchAll = $this->matchAll) {
            if (\is_string($matchAll) && $matchAll{0} === '/') {
                $path = $matchAll;
            } elseif (\is_callable($matchAll)) {
                return [self::FOUND, $path, [
                    'handler' => $matchAll,
                ]];
            }
        }

        $path = $this->formatUriPath($path, $this->ignoreLastSlash);
        $method = strtoupper($method);

        // find in route caches.
        if ($this->cacheRoutes && isset($this->cacheRoutes[$path][$method])) {
            return [self::FOUND, $path, $this->cacheRoutes[$path][$method]];
        }

        // is a static route path
        if ($this->staticRoutes && ($routeInfo = $this->findInStaticRoutes($path, $method))) {
            return [self::FOUND, $path, $routeInfo];
        }

        $first = null;
        $allowedMethods = [];

        // eg '/article/12'
        if ($pos = strpos($path, '/', 1)) {
            $first = substr($path, 1, $pos - 1);
        }

        // is a regular dynamic route(the first node is 1th level index key).
        if ($first && isset($this->regularRoutes[$first])) {
            $result = $this->findInRegularRoutes($this->regularRoutes[$first], $path, $method);

            if ($result[0] === self::FOUND) {
                return $result;
            }

            $allowedMethods = $result[1];
        }

        // is a irregular dynamic route
        if (isset($this->vagueRoutes[$method])) {
            $result = $this->findInVagueRoutes($this->vagueRoutes[$method], $path, $method);

            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        // For HEAD requests, attempt fallback to GET
        if ($method === 'HEAD') {
            if (isset($this->cacheRoutes[$path]['GET'])) {
                return [self::FOUND, $path, $this->cacheRoutes[$path]['GET']];
            }

            if ($routeInfo = $this->findInStaticRoutes($path, 'GET')) {
                return [self::FOUND, $path, $routeInfo];
            }

            if ($first && isset($this->regularRoutes[$first])) {
                $result = $this->findInRegularRoutes($this->regularRoutes[$first], $path, 'GET');

                if ($result[0] === self::FOUND) {
                    return $result;
                }
            }

            if (isset($this->vagueRoutes['GET'])) {
                $result = $this->findInVagueRoutes($this->vagueRoutes['GET'], $path, 'GET');

                if ($result[0] === self::FOUND) {
                    return $result;
                }
            }
        }

        // If nothing else matches, try fallback routes. $router->any('*', 'handler');
        if ($this->staticRoutes && ($routeInfo = $this->findInStaticRoutes('/*', $method))) {
            return [self::FOUND, $path, $routeInfo];
        }

        if ($this->notAllowedAsNotFound) {
            return [self::NOT_FOUND, $path, null];
        }

        // collect allowed methods from: staticRoutes, vagueRoutes OR return not found.
        return $this->findAllowedMethods($path, $method, $allowedMethods);
    }

    /*******************************************************************************
     * helper methods
     ******************************************************************************/

    /**
     * @param string $path
     * @param string $method
     * @param array $allowedMethods
     * @return array
     */
    protected function findAllowedMethods(string $path, string $method, array $allowedMethods): array
    {
        if (isset($this->staticRoutes[$path])) {
            $allowedMethods = \array_merge($allowedMethods, \array_keys($this->staticRoutes[$path]));
        }

        foreach ($this->vagueRoutes as $m => $routes) {
            if ($method === $m) {
                continue;
            }

            $result = $this->findInVagueRoutes($this->vagueRoutes['GET'], $path, $m);

            if ($result[0] === self::FOUND) {
                $allowedMethods[] = $method;
            }
        }

        if ($allowedMethods && ($list = \array_unique($allowedMethods))) {
            return [self::METHOD_NOT_ALLOWED, $path, $list];
        }

        // oo ... not found
        return [self::NOT_FOUND, $path, null];
    }

    /**
     * @param string $path
     * @param string $method
     * @return array|false
     */
    protected function findInStaticRoutes($path, $method)
    {
        if (isset($this->staticRoutes[$path][$method])) {
            return $this->staticRoutes[$path][$method];
        }

        return false;
    }

    /**
     * @param array $routesData
     * @param string $path
     * @param string $method
     * @return array
     */
    protected function findInRegularRoutes(array $routesData, string $path, string $method): array
    {
        $allowedMethods = '';

        foreach ($routesData as $conf) {
            if (0 === \strpos($path, $conf['start']) && \preg_match($conf['regex'], $path, $matches)) {
                $allowedMethods .= $conf['methods'];

                if (false !== \strpos($conf['methods'], $method . ',')) {
                    $this->filterMatches($matches, $conf);

                    if ($this->tmpCacheNumber > 0) {
                        $this->cacheMatchedParamRoute($path, $method, $conf);
                    }

                    return [self::FOUND, $path, $conf];
                }
            }
        }

        return [
            self::NOT_FOUND,
            $allowedMethods ? \explode(',', \rtrim($allowedMethods, ',')) : []
        ];
    }

    /**
     * @param array $routesData
     * @param string $path
     * @param string $method
     * @return array
     */
    protected function findInVagueRoutes(array $routesData, string $path, string $method): array
    {
        foreach ($routesData as $conf) {
            if ($conf['include'] && false === \strpos($path, $conf['include'])) {
                continue;
            }

            if (\preg_match($conf['regex'], $path, $matches)) {
                $this->filterMatches($matches, $conf);

                if ($this->tmpCacheNumber > 0) {
                    $this->cacheMatchedParamRoute($path, $method, $conf);
                }

                return [self::FOUND, $path, $conf];
            }
        }

        return [self::NOT_FOUND];
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $conf
     */
    protected function cacheMatchedParamRoute(string $path, string $method, array $conf)
    {
        $cacheNumber = (int)$this->tmpCacheNumber;

        // cache last $cacheNumber routes.
        if ($cacheNumber > 0 && !isset($this->cacheRoutes[$path][$method])) {
            if ($this->cacheCounter >= $cacheNumber) {
                \array_shift($this->cacheRoutes);
            }

            $this->cacheCounter++;
            $this->cacheRoutes[$path][$method] = $conf;
        }
    }

    /**
     * @return array[]
     */
    public function getCacheRoutes(): array
    {
        return $this->cacheRoutes;
    }

    /**
     * @return int
     */
    public function getCacheCounter(): int
    {
        return $this->cacheCounter;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->routeCounter;
    }

    /**
     * @return array
     */
    public function getGlobalOptions(): array
    {
        return $this->globalOptions;
    }

    /**
     * @param array $globalOptions
     * @return $this
     */
    public function setGlobalOptions(array $globalOptions): self
    {
        $this->globalOptions = $globalOptions;

        return $this;
    }

    /*******************************************************************************
     * other helper methods
     ******************************************************************************/

    /**
     * get handler from router
     *
     * @param array ...$params
     *
     * @return array
     */
    public function getHandler(...$params): array
    {
        list($path, $method) = $params;
        // list($path, $info) = $router;

        return $this->match($path, $method);
    }

    /**
     * 自动注册路由
     *
     * @param array $requestMapping
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function registerRoutes(array $requestMapping)
    {
        foreach ($requestMapping as $className => $mapping) {
            if (!isset($mapping['prefix'], $mapping['routes'])) {
                continue;
            }

            // controller prefix
            $controllerPrefix = $mapping['prefix'];
            $controllerPrefix = $this->getControllerPrefix($controllerPrefix, $className);
            $routes           = $mapping['routes'];

            // 注册控制器对应的一组路由
            $this->registerRoute($className, $routes, $controllerPrefix);
        }
    }

    /**
     * 注册路由
     * @param string $className 类名
     * @param array $routes 控制器对应的路由组
     * @param string $controllerPrefix 控制器prefix
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    private function registerRoute(string $className, array $routes, string $controllerPrefix)
    {
        $controllerPrefix = '/' . \trim($controllerPrefix, '/');

        // Circular Registration Route
        foreach ($routes as $route) {
            if (!isset($route['route'], $route['method'], $route['action'])) {
                continue;
            }
            $mapRoute = $route['route'];
            $method   = $route['method'];
            $action   = $route['action'];

            // 解析注入action名称
            $mapRoute = empty($mapRoute) ? $action : $mapRoute;

            // '/'开头的路由是一个单独的路由 未使用'/'需要和控制器组拼成一个路由
            $uri     = $mapRoute[0] === '/' ? $mapRoute : $controllerPrefix . '/' . $mapRoute;
            $handler = $className . '@' . $action;

            // 注入路由规则
            $this->map($method, $uri, $handler, [
                'params' => $route['params'] ?? []
            ]);
        }
    }

    /**
     * 获取控制器prefix
     *
     * @param string $controllerPrefix 注解控制器prefix
     * @param string $className        控制器类名
     *
     * @return string
     */
    private function getControllerPrefix(string $controllerPrefix, string $className): string
    {
        // 注解注入不为空，直接返回prefix
        if (!empty($controllerPrefix)) {
            return $controllerPrefix;
        }

        // 注解注入为空，解析控制器prefix
        $reg    = '/^.*\\\(\w+)' . $this->controllerSuffix . '$/';
        $prefix = '';

        if ($result = \preg_match($reg, $className, $match)) {
            $prefix = '/' . \lcfirst($match[1]);
        }

        return $prefix;
    }
}
