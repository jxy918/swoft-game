<?php

namespace Swoft\Http\Server\Router;

/**
 * Class AbstractRouter
 * @package Swoft\Http\Server\Router
 * @author inhere <in.798@qq.com>
 *
 * @method get(string $route, mixed $handler, array $opts = [])
 * @method post(string $route, mixed $handler, array $opts = [])
 * @method put(string $route, mixed $handler, array $opts = [])
 * @method delete(string $route, mixed $handler, array $opts = [])
 * @method options(string $route, mixed $handler, array $opts = [])
 * @method head(string $route, mixed $handler, array $opts = [])
 * @method search(string $route, mixed $handler, array $opts = [])
 * @method connect(string $route, mixed $handler, array $opts = [])
 * @method trace(string $route, mixed $handler, array $opts = [])
 * @method any(string $route, mixed $handler, array $opts = [])
 */
abstract class AbstractRouter implements RouterInterface
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * some available patterns regex
     * $router->get('/user/{id}', 'handler');
     * @var array
     */
    protected static $globalParams = [
        'all' => '.*',
        'any' => '[^/]+',        // match any except '/'
        'num' => '[1-9][0-9]*',  // match a number and gt 0
        'int' => '\d+',          // match a number
        'act' => '[a-zA-Z][\w-]+', // match a action name
    ];

    /** @var bool */
    protected $initialized = false;

    /** @var string */
    protected $currentGroupPrefix;

    /** @var array */
    protected $currentGroupOption;

    /**
     * static Routes - no dynamic argument match
     * 整个路由 path 都是静态字符串 e.g. '/user/login'
     * @var array[]
     * [
     *     '/user/login' => [
     *          // METHOD => [...]
     *          'GET' => [
     *              'handler' => 'handler',
     *              'option' => [...],
     *          ],
     *          'PUT' => [
     *              'handler' => 'handler',
     *              'option' => [...],
     *          ],
     *          ...
     *      ],
     *      ... ...
     * ]
     */
    protected $staticRoutes = [];

    /**
     * regular Routes - have dynamic arguments, but the first node is normal string.
     * 第一节是个静态字符串，称之为有规律的动态路由。按第一节的信息进行分组存储
     * e.g '/hello/{name}' '/user/{id}'
     * @var array[]
     * [
     *     // 使用完整的第一节作为key进行分组
     *     'add' => [
     *          [
     *              'start' => '/add/',
     *              'regex' => '/add/(\w+)',
     *              'methods' => 'GET',
     *              'handler' => 'handler',
     *              'option' => [...],
     *          ],
     *          ...
     *      ],
     *     'blog' => [
     *        [
     *              'start' => '/blog/post-',
     *              'regex' => '/blog/post-(\w+)',
     *              'methods' => 'GET',
     *              'handler' => 'handler',
     *              'option' => [...],
     *        ],
     *        ...
     *     ],
     *     ... ...
     * ]
     */
    protected $regularRoutes = [];

    /**
     * vague Routes - have dynamic arguments,but the first node is exists regex.
     * 第一节就包含了正则匹配，称之为无规律/模糊的动态路由
     * e.g '/{name}/profile' '/{some}/{some2}'
     * @var array
     * [
     *     // 使用 HTTP METHOD 作为 key进行分组
     *     'GET' => [
     *          [
     *              // 必定包含的字符串
     *              'include' => '/profile',
     *              'regex' => '/(\w+)/profile',
     *              'handler' => 'handler',
     *              'option' => [...],
     *          ],
     *          ...
     *     ],
     *     'POST' => [
     *          [
     *              'include' => null,
     *              'regex' => '/(\w+)/(\w+)',
     *              'handler' => 'handler',
     *              'option' => [...],
     *          ],
     *          ...
     *     ],
     *      ... ...
     * ]
     */
    protected $vagueRoutes = [];

    /*******************************************************************************
     * router config
     ******************************************************************************/

    /**
     * Match all request.
     * 1. If is a valid URI path, will matchAll all request uri to the path.
     * 2. If is a closure, will matchAll all request then call it
     * eg: '/site/maintenance' or `function () { echo 'System Maintaining ... ...'; }`
     * @var mixed
     */
    public $matchAll;

    /**
     * Ignore last slash char('/'). If is True, will clear last '/'.
     * @var bool
     */
    public $ignoreLastSlash = false;

    /**
     * @var bool NotAllowed As NotFound. If True, only two status value will be return(FOUND, NOT_FOUND).
     */
    public $notAllowedAsNotFound = false;

    /**
     * Controller suffix, is valid when '$autoRoute' = true. eg: 'Controller'
     * @var string
     */
    public $controllerSuffix = 'Controller';

    /**
     * object creator.
     * @param array $config
     * @return self
     * @throws \LogicException
     */
    public static function make(array $config = []): AbstractRouter
    {
        return new static($config);
    }

    /**
     * object constructor.
     * @param array $config
     * @throws \LogicException
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);

        $this->currentGroupPrefix = '';
        $this->currentGroupOption = [];
    }

    /**
     * @param array $config
     * @throws \LogicException
     */
    public function setConfig(array $config)
    {
        if ($this->initialized) {
            throw new \LogicException('Routing has been added, and configuration is not allowed!');
        }

        $props = [
            'name' => 1,
            'matchAll' => 1,
            'tmpCacheNumber' => 1,
            'ignoreLastSlash' => 1,
            'notAllowedAsNotFound' => 1,
            'controllerSuffix' => 1,
        ];

        foreach ($config as $name => $value) {
            if (isset($props[$name])) {
                $this->$name = $value;
            }
        }
    }

    /*******************************************************************************
     * route collection
     ******************************************************************************/

    /**
     * Defines a route callback and method
     * @param string $method
     * @param array $args
     * @return static
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function __call($method, array $args)
    {
        if (\in_array(\strtoupper($method), self::ALLOWED_METHODS, true)) {
            if (\count($args) < 2) {
                throw new \InvalidArgumentException("The method [$method] parameters is missing.");
            }

            return $this->map($method, ...$args);
        }

        throw new \InvalidArgumentException("The method [$method] not exists in the class.");
    }

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     * @ref package 'nikic/fast-route'
     * @param string $prefix
     * @param \Closure $callback
     * @param array $opts
     */
    public function group(string $prefix, \Closure $callback, array $opts = [])
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix = $previousGroupPrefix . '/' . \trim($prefix, '/');

        $previousGroupOption = $this->currentGroupOption;
        $this->currentGroupOption = $opts;

        $callback($this);

        $this->currentGroupPrefix = $previousGroupPrefix;
        $this->currentGroupOption = $previousGroupOption;
    }

    /**
     * validate and format arguments
     * @param string|array $methods
     * @param mixed $handler
     * @return array
     * @throws \InvalidArgumentException
     */
    public function validateArguments($methods, $handler): array
    {
        if (!$methods || !$handler) {
            throw new \InvalidArgumentException('The method and route handler is not allow empty.');
        }

        $allow = self::ALLOWED_METHODS_STR . ',';
        $hasAny = false;

        $methods = \array_map(function ($m) use ($allow, &$hasAny) {
            $m = \strtoupper(trim($m));

            if (!$m || false === \strpos($allow, $m . ',')) {
                throw new \InvalidArgumentException("The method [$m] is not supported, Allow: " . trim($allow, ','));
            }

            if (!$hasAny && $m === self::ANY) {
                $hasAny = true;
            }

            return $m;
        }, (array)$methods);

        return $hasAny ? self::ALLOWED_METHODS : $methods;
    }

    /**
     * is Static Route
     * @param string $route
     * @return bool
     */
    public static function isStaticRoute(string $route): bool
    {
        return \strpos($route, '{') === false && \strpos($route, '[') === false;
    }

    /**
     * @param string $path
     * @param bool $ignoreLastSlash
     * @return string
     */
    protected function formatUriPath(string $path, $ignoreLastSlash): string
    {
        // clear '//', '///' => '/'
        if (false !== \strpos($path, '//')) {
            $path = (string)\preg_replace('#\/\/+#', '/', $path);
        }

        // decode
        $path = \rawurldecode($path);

        // setting 'ignoreLastSlash'
        if ($path !== '/' && $ignoreLastSlash) {
            $path = \rtrim($path, '/');
        }

        return $path;
    }

    /**
     * @param array $matches
     * @param array $conf
     */
    protected function filterMatches(array $matches, array &$conf)
    {
        if (!$matches) {
            $conf['matches'] = [];
            return;
        }

        // clear all int key
        $matches = \array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY);

        // apply some default param value
        if (isset($conf['option']['defaults'])) {
            $conf['matches'] = \array_merge($conf['option']['defaults'], $matches);
        } else {
            $conf['matches'] = $matches;
        }
    }

    /**
     * parse param route
     * @param string $route
     * @param array $params
     * @param array $conf
     * @return array
     * @throws \LogicException
     */
    public function parseParamRoute(string $route, array $params, array $conf): array
    {
        $bak = $route;
        $noOptional = null;

        // 解析可选参数位
        if (false !== ($pos = \strpos($route, '['))) {
            // $hasOptional = true;
            $noOptional = \substr($route, 0, $pos);
            $withoutClosingOptionals = rtrim($route, ']');
            $optionalNum = \strlen($route) - \strlen($withoutClosingOptionals);

            if ($optionalNum !== \substr_count($withoutClosingOptionals, '[')) {
                throw new \LogicException('Optional segments can only occur at the end of a route');
            }

            // '/hello[/{name}]' -> '/hello(?:/{name})?'
            $route = \str_replace(['[', ']'], ['(?:', ')?'], $route);
        }

        // quote '.','/' to '\.','\/'
        if (false !== \strpos($route, '.')) {
            // $route = preg_quote($route, '/');
            $route = \str_replace('.', '\.', $route);
        }

        // 解析参数，替换为对应的 正则
        if (\preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_-]*)\}#', $route, $m)) {
            /** @var array[] $m */
            $replacePairs = [];

            foreach ($m[1] as $name) {
                $key = '{' . $name . '}';
                $regex = $params[$name] ?? self::DEFAULT_REGEX;

                // 将匹配结果命名 (?P<arg1>[^/]+)
                $replacePairs[$key] = '(?P<' . $name . '>' . $regex . ')';
                // $replacePairs[$key] = '(' . $regex . ')';
            }

            $route = \strtr($route, $replacePairs);
        }

        // 分析路由字符串是否是有规律的
        $first = null;
        $conf['regex'] = '#^' . $route . '$#';

        // first node is a normal string
        // e.g '/user/{id}' first: 'user', '/a/{post}' first: 'a'
        if (\preg_match('#^/([\w-]+)/[\w-]*/?#', $bak, $m)) {
            $first = $m[1];
            $conf['start'] = $m[0];

            return [$first, $conf];
        }

        // first node contain regex param '/hello[/{name}]' '/{some}/{some2}/xyz'
        $include = null;

        if ($noOptional) {
            if (\strpos($noOptional, '{') === false) {
                $include = $noOptional;
            } else {
                $bak = $noOptional;
            }
        }

        if (!$include && \preg_match('#/([\w-]+)/?[\w-]*#', $bak, $m)) {
            $include = $m[0];
        }

        $conf['include'] = $include;

        return [$first, $conf];
    }

    /**
     * @param array $routesData
     * @param string $path
     * @param string $method
     * @return array
     */
    abstract protected function findInRegularRoutes(array $routesData, string $path, string $method): array;

    /**
     * @param array $routesData
     * @param string $path
     * @param string $method
     * @return array
     */
    abstract protected function findInVagueRoutes(array $routesData, string $path, string $method): array;

    /**
     * @param array $tmpParams
     * @return array
     */
    public function getAvailableParams(array $tmpParams): array
    {
        $params = self::$globalParams;

        if ($tmpParams) {
            foreach ($tmpParams as $name => $pattern) {
                $key = \trim($name, '{}');
                $params[$key] = $pattern;
            }
        }

        return $params;
    }

    /**
     * convert 'first-second' to 'firstSecond'
     * @param string $str
     * @return string
     */
    public static function convertNodeStr($str): string
    {
        $str = \trim($str, '-');

        // convert 'first-second' to 'firstSecond'
        if (\strpos($str, '-')) {
            $str = (string)\preg_replace_callback('/-+([a-z])/', function ($c) {
                return \strtoupper($c[1]);
            }, \trim($str, '- '));
        }

        return $str;
    }

    /**
     * @param array $params
     */
    public function addGlobalParams(array $params)
    {
        foreach ($params as $name => $pattern) {
            $this->addGlobalParam($name, $pattern);
        }
    }

    /**
     * @param $name
     * @param $pattern
     */
    public function addGlobalParam($name, $pattern)
    {
        $name = \trim($name, '{} ');
        self::$globalParams[$name] = $pattern;
    }

    /**
     * @return array
     */
    public static function getGlobalParams(): array
    {
        return self::$globalParams;
    }

    /**
     * @return array
     */
    public static function getSupportedMethods(): array
    {
        return self::ALLOWED_METHODS;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param array $staticRoutes
     */
    public function setStaticRoutes(array $staticRoutes)
    {
        $this->staticRoutes = $staticRoutes;
    }

    /**
     * @return array
     */
    public function getStaticRoutes(): array
    {
        return $this->staticRoutes;
    }

    /**
     * @param \array[] $regularRoutes
     */
    public function setRegularRoutes(array $regularRoutes)
    {
        $this->regularRoutes = $regularRoutes;
    }

    /**
     * @return \array[]
     */
    public function getRegularRoutes(): array
    {
        return $this->regularRoutes;
    }

    /**
     * @param array $vagueRoutes
     */
    public function setVagueRoutes(array $vagueRoutes)
    {
        $this->vagueRoutes = $vagueRoutes;
    }

    /**
     * @return array
     */
    public function getVagueRoutes(): array
    {
        return $this->vagueRoutes;
    }
}
