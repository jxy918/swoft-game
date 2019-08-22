<?php declare(strict_types=1);

namespace Swoft\Http\Server\Router;

use function array_keys;
use function array_merge;
use function array_shift;
use ArrayIterator;
use Closure;
use function count;
use function implode;
use InvalidArgumentException;
use LogicException;
use function rtrim;
use function strpos;
use function strtoupper;
use function substr;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Server\Contract\RouterInterface;
use Swoft\Http\Server\Helper\RouteHelper;
use Traversable;
use function trim;

/**
 * Class Router - This is object version
 *
 * @Bean("httpRouter")
 *
 * @since 2.0
 */
class Router implements RouterInterface
{
    use RouterConfigTrait;

    /** @var int */
    protected $routeCounter = 0;

    /** @var callable[] Router middleware handler chains */
    private $chains = [];

    // -- Group info

    /** @var string */
    protected $currentGroupPrefix;
    /** @var array */
    protected $currentGroupOption = [];
    /** @var array */
    protected $currentGroupChains = [];

    // -- Routes data

    /**
     * name routes. use for find a route by name.
     * @var array [name => Route]
     */
    protected $namedRoutes = [];

    /**
     * static Routes - no dynamic argument match. e.g. '/user/login'
     * @var Route[]
     * [
     *     'GET /user/login' =>  Route,
     *     'POST /user/login' =>  Route,
     * ]
     */
    protected $staticRoutes = [];

    /**
     * regular Routes - have dynamic arguments, but the first node is normal string.
     *
     * @var Route[][]
     * [
     *     // Group the entire first section as a key
     *     'edit' => [
     *          Route, // '/edit/{id}'
     *      ],
     *     'blog' => [
     *        Route, // '/blog/post-{id}'
     *     ],
     * ]
     */
    protected $regularRoutes = [];

    /**
     * vague Routes - have dynamic arguments,but the first node is exists regex.
     *
     * @var Route[][]
     * [
     *     // Group using HTTP METHOD as key
     *     'GET' => [
     *          Route, // '/{name}/profile'
     *          ...
     *     ],
     *     'POST' => [
     *          Route, // '/{some}/{some2}'
     *          ...
     *     ],
     * ]
     */
    protected $vagueRoutes = [];

    /**
     * There are latest route caches. like static routes
     * @see $staticRoutes
     * @var Route[]
     * [
     *  'GET /user/login' => Route,
     *  'PUT /user/login' => Route,
     * ]
     */
    private $cacheRoutes = [];

    /**
     * object creator.
     *
     * @param array $config
     *
     * @return self
     * @throws LogicException
     */
    public static function create(array $config = []): Router
    {
        return new static($config);
    }

    /**
     * object constructor.
     *
     * @param array $config
     *
     * @throws LogicException
     */
    public function __construct(array $config = [])
    {
        $this->config($config);
        $this->currentGroupPrefix = '';
        $this->currentGroupOption = [];
    }

    /*******************************************************************************
     * router middleware
     ******************************************************************************/

    /**
     * alias of the method: middleware()
     *
     * @param array ...$middleware
     *
     * @return self
     */
    public function use(...$middleware): Router
    {
        return $this->middleware(...$middleware);
    }

    /**
     * push middleware(s) for the route
     *
     * @param mixed ...$middleware
     *
     * @return Router
     */
    public function middleware(...$middleware): Router
    {
        foreach ($middleware as $handler) {
            $this->chains[] = $handler;
        }

        return $this;
    }

    /*******************************************************************************
     * route register
     ******************************************************************************/

    /**
     * register a route, allow GET request method.
     * {@inheritdoc}
     */
    public function get(string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        return $this->add('GET', $path, $handler, $pathParams, $opts);
        // return $this->map(['GET', 'HEAD'], $path, $handler, $pathParams, $opts);
    }

    /**
     * register a route, allow POST request method.
     * {@inheritdoc}
     */
    public function post(string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        return $this->add('POST', $path, $handler, $pathParams, $opts);
    }

    /**
     * register a route, allow PUT request method.
     * {@inheritdoc}
     */
    public function put(string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        return $this->add('PUT', $path, $handler, $pathParams, $opts);
    }

    /**
     * register a route, allow PATCH request method.
     * {@inheritdoc}
     */
    public function patch(string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        return $this->add('PATCH', $path, $handler, $pathParams, $opts);
    }

    /**
     * register a route, allow DELETE request method.
     * {@inheritdoc}
     */
    public function delete(string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        return $this->add('DELETE', $path, $handler, $pathParams, $opts);
    }

    /**
     * register a route, allow HEAD request method.
     * {@inheritdoc}
     */
    public function head(string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        return $this->add('HEAD', $path, $handler, $pathParams, $opts);
    }

    /**
     * register a route, allow OPTIONS request method.
     * {@inheritdoc}
     */
    public function options(string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        return $this->add('OPTIONS', $path, $handler, $pathParams, $opts);
    }

    /**
     * register a route, allow CONNECT request method.
     * {@inheritdoc}
     */
    public function connect(string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        return $this->add('CONNECT', $path, $handler, $pathParams, $opts);
    }

    /**
     * register a route, allow any request METHOD.
     * {@inheritdoc}
     */
    public function any(string $path, $handler, array $pathParams = [], array $opts = []): void
    {
        $this->map(self::METHODS_ARRAY, $path, $handler, $pathParams, $opts);
    }

    /**
     * @param array|string    $methods
     * @param string          $path
     * @param callable|string $handler
     * @param array           $pathParams
     * @param array           $opts
     */
    public function map($methods, string $path, $handler, array $pathParams = [], array $opts = [])
    {
        foreach ((array)$methods as $method) {
            $this->add($method, $path, $handler, $pathParams, $opts);
        }
    }

    /**
     * @param string $method
     * @param string $path
     * @param        $handler
     * @param array  $pathParams
     * @param array  $opts
     *
     * @return Route
     */
    public function add(string $method, string $path, $handler, array $pathParams = [], array $opts = []): Route
    {
        if (!$method || !$handler) {
            throw new InvalidArgumentException('The method and route handler is not allow empty.');
        }

        $method = strtoupper($method);

        if ($method === 'ANY') {
            $this->any($path, $handler, $pathParams, $opts);
            return Route::createFromArray();
        }

        if (false === strpos(self::METHODS_STRING, ',' . $method . ',')) {
            throw new InvalidArgumentException(
                "The method [$method] is not supported, Allow: " . trim(self::METHODS_STRING, ',')
            );
        }

        // create Route
        $route = Route::create($method, $path, $handler, $pathParams, $opts);

        return $this->addRoute($route);
    }

    /**
     * @param Route $route
     *
     * @return Route
     */
    public function addRoute(Route $route): Route
    {
        $this->routeCounter++;
        $this->appendGroupInfo($route);

        $path   = $route->getPath();
        $method = $route->getMethod();

        // Has route name.
        if ($name = $route->getName()) {
            $this->namedRoutes[$name] = $route;
        }

        // It is static route
        if (RouteHelper::isStaticRoute($path)) {
            $this->staticRoutes[$method . ' ' . $path] = $route;
            return $route;
        }

        // Parse param route
        // if the first node is static string.
        if ($first = $route->parseParam(self::$globalParams)) {
            $this->regularRoutes[$method . ' ' . $first][] = $route;
        } else {
            $this->vagueRoutes[$method][] = $route;
        }

        return $route;
    }

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string   $prefix
     * @param Closure $callback
     * @param array    $middleware
     * @param array    $opts
     */
    public function group(string $prefix, Closure $callback, array $middleware = [], array $opts = []): void
    {
        // Backups
        $previousGroupPrefix = $this->currentGroupPrefix;
        $previousGroupOption = $this->currentGroupOption;
        $previousGroupChains = $this->currentGroupChains;

        $this->currentGroupOption = $opts;
        $this->currentGroupChains = $middleware;
        $this->currentGroupPrefix = $previousGroupPrefix . '/' . trim($prefix, '/');

        // Run callback.
        $callback($this);

        // Reverts
        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupOption = $previousGroupOption;
        $this->currentGroupChains = $previousGroupChains;
    }

    /**
     * prepare for add
     *
     * @param Route $route
     *
     * @return void
     */
    protected function appendGroupInfo(Route $route): void
    {
        $path = $route->getPath();

        // Always add '/' prefix.
        $path = strpos($path, '/') === 0 ? $path : '/' . $path;
        $path = $this->currentGroupPrefix . $path;

        // Setting 'ignoreLastSlash'
        if ($path !== '/' && $this->ignoreLastSlash) {
            $path = rtrim($path, '/');
        }

        // Re-set formatted path
        $route->setPath($path);

        if ($grpOptions = $this->currentGroupOption) {
            $route->setOptions(array_merge($grpOptions, $route->getOptions()));
        }

        if ($grpChains = $this->currentGroupChains) {
            // prepend group middleware at before.
            $route->setChains(array_merge($grpChains, $route->getChains()));
        }
    }

    /*******************************************************************************
     * route match
     ******************************************************************************/

    /**
     * Find the matched route info for the given request uri path
     *
     * @param string $method
     * @param string $path
     *
     * @return array returns array.
     * [
     *  match status, // found, not found, method not allowed
     *  formatted path,
     *  (Route object) OR (allowed methods list)
     * ]
     */
    public function match(string $path, string $method = 'GET'): array
    {
        $path   = RouteHelper::formatPath($path, $this->ignoreLastSlash);
        $method = strtoupper($method);
        $sKey   = $method . ' ' . $path;

        // It is a static route path
        if (isset($this->staticRoutes[$sKey])) {
            return [self::FOUND, $path, $this->staticRoutes[$sKey]];
        }

        // Find in route caches.
        if ($this->cacheRoutes && isset($this->cacheRoutes[$sKey])) {
            return [self::FOUND, $path, $this->cacheRoutes[$sKey]];
        }

        // It is a dynamic route, match by regexp
        $result = $this->matchDynamicRoute($path, $method);
        if ($result[0] === self::FOUND) { // will cache param route.
            $this->cacheMatchedParamRoute($path, $method, $result[2]);
            return $result;
        }

        // !!Don't support. handle Auto Route. always return new Route object.
        // if ($this->autoRoute && ($handler = $this->matchAutoRoute($path))) {
        //     return [self::FOUND, $path, Route::create($method, $path, $handler)];
        // }

        // For HEAD requests, attempt fallback to GET
        if ($method === 'HEAD') {
            $sKey = 'GET ' . $path;
            if (isset($this->staticRoutes[$sKey])) {
                return [self::FOUND, $path, $this->staticRoutes[$sKey]];
            }

            if ($this->cacheRoutes && isset($this->cacheRoutes[$sKey])) {
                return [self::FOUND, $path, $this->cacheRoutes[$sKey]];
            }

            $result = $this->matchDynamicRoute($path, 'GET');
            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        // If nothing else matches, try fallback routes. $router->any('*', 'handler');
        $sKey = $method . ' /*';
        if (isset($this->staticRoutes[$sKey])) {
            return [self::FOUND, $path, $this->staticRoutes[$sKey]];
        }

        // Collect allowed methods from: staticRoutes, vagueRoutes OR return not found.
        if ($this->handleMethodNotAllowed) {
            return $this->findAllowedMethods($path, $method);
        }

        return [self::NOT_FOUND, $path, null];
    }

    /**
     * It is a dynamic route, match by regexp
     *
     * @param string $path
     * @param string $method
     *
     * @return array
     * [
     *  status,
     *  path,
     *  Route(object) -> it's a raw Route clone.
     * ]
     */
    protected function matchDynamicRoute(string $path, string $method): array
    {
        $fKey = $first = '';
        if ($pos = strpos($path, '/', 1)) {
            $first = substr($path, 1, $pos - 1);
            $fKey  = $method . ' ' . $first;
        }

        // It is a regular dynamic route(the first node is 1th level index key).
        if ($fKey && $routeList = $this->regularRoutes[$fKey] ?? []) {
            /** @var Route $route */
            foreach ($routeList as $route) {
                $result = $route->match($path);
                if ($result[0]) {
                    return [self::FOUND, $path, $route->copyWithParams($result[1])];
                }
            }
        }

        // It is a irregular dynamic route
        if ($routeList = $this->vagueRoutes[$method] ?? []) {
            foreach ($routeList as $route) {
                $result = $route->match($path);
                if ($result[0]) {
                    return [self::FOUND, $path, $route->copyWithParams($result[1])];
                }
            }
        }

        return [self::NOT_FOUND, $path, null];
    }

    /**
     * @param string $path
     * @param string $method
     *
     * @return array
     */
    protected function findAllowedMethods(string $path, string $method): array
    {
        $methodNames = [];
        foreach (self::METHODS_ARRAY as $m) {
            if ($method === $m) {
                continue;
            }

            $sKey = $m . ' ' . $path;
            if (isset($this->staticRoutes[$sKey])) {
                $methodNames[$m] = 1;
                continue;
            }

            $result = $this->matchDynamicRoute($path, $m);
            if ($result[0] === self::FOUND) {
                $methodNames[$m] = 1;
            }
        }

        if ($methodNames) {
            return [self::METHOD_NOT_ALLOWED, $path, array_keys($methodNames)];
        }
        return [self::NOT_FOUND, $path, null];
    }

    /**
     * @param string $path
     * @param string $method
     * @param Route  $route
     */
    protected function cacheMatchedParamRoute(string $path, string $method, Route $route): void
    {
        $cacheKey = $method . ' ' . $path;
        $cacheNum = (int)$this->tmpCacheNumber;

        // Cache last $cacheNumber routes.
        if ($cacheNum > 0 && !isset($this->cacheRoutes[$cacheKey])) {
            if ($this->cacheCount() >= $cacheNum) {
                array_shift($this->cacheRoutes);
            }

            $this->cacheRoutes[$cacheKey] = $route;
        }
    }

    /*******************************************************************************
     * helper methods
     ******************************************************************************/

    /**
     * @param string $name Route name
     * @param array  $pathVars
     *
     * @return string
     */
    public function createUri(string $name, array $pathVars = []): string
    {
        if ($route = $this->getRoute($name)) {
            return $route->toUri($pathVars);
        }

        return '';
    }

    /**
     * @param string $name
     * @param Route  $route
     */
    public function nameRoute(string $name, Route $route): void
    {
        if ($name = trim($name)) {
            $this->namedRoutes[$name] = $route;
        }
    }

    /**
     * clear cached routes
     */
    public function clearCacheRoutes(): void
    {
        $this->cacheRoutes = [];
    }

    /**
     * @return int
     */
    public function cacheCount(): int
    {
        return count($this->cacheRoutes);
    }

    /**
     * get a name route by given name.
     *
     * @param string $name
     *
     * @return Route|null
     */
    public function getRoute(string $name): ?Route
    {
        return $this->namedRoutes[$name] ?? null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->routeCounter;
    }

    /**
     * @param Closure $func
     */
    public function each(Closure $func): void
    {
        /** @var Route $route */
        foreach ($this->staticRoutes as $route) {
            $func($route);
        }

        foreach ($this->regularRoutes as $routes) {
            foreach ($routes as $route) {
                $func($route);
            }
        }

        foreach ($this->vagueRoutes as $routes) {
            foreach ($routes as $route) {
                $func($route);
            }
        }
    }

    /**
     * get all routes
     * @return array
     */
    public function getRoutes(): array
    {
        $routes = [];
        $this->each(function (Route $route) use (&$routes) {
            $routes[] = $route->toArray();
        });

        return $routes;
    }

    /**
     * @return array
     */
    public function getChains(): array
    {
        return $this->chains;
    }

    /**
     * Retrieve an external iterator
     * @link  https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getRoutes());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param callable|null $filter
     *
     * @return string
     */
    public function toString(callable $filter = null): string
    {
        $count  = 0;
        $indent = '  ';

        $strings   = ['# Routes Number: ' . $this->count()];
        $strings[] = "\n# Static Routes:";

        /** @var Route $route */
        foreach ($this->staticRoutes as $route) {
            $routeString = $route->toString();

            if (!$filter || $filter($routeString)) {
                $count++;
                $strings[] = $indent . $routeString;
            }
        }

        $strings[] = "\n# Regular Routes:";
        foreach ($this->regularRoutes as $routes) {
            foreach ($routes as $route) {
                $routeString = $route->toString();

                if (!$filter || $filter($routeString)) {
                    $count++;
                    $strings[] = $indent . $routeString;
                }
            }
        }

        $strings[] = "\n# Vague Routes:";
        foreach ($this->vagueRoutes as $routes) {
            foreach ($routes as $route) {
                $routeString = $route->toString();

                if (!$filter || $filter($routeString)) {
                    $count++;
                    $strings[] = $indent . $routeString;
                }
            }
        }

        $strings[] = "\nCurrent displayed count: $count";

        return implode("\n", $strings);
    }
}
