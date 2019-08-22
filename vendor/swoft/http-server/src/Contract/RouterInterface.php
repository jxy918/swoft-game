<?php declare(strict_types=1);

namespace Swoft\Http\Server\Contract;

use Countable;
use IteratorAggregate;
use Swoft\Http\Server\Router\Route;

/**
 * Interface RouterInterface
 * @since 1.0
 */
interface RouterInterface extends IteratorAggregate, Countable, \Swoft\Contract\RouterInterface
{
    public const METHOD_NOT_ALLOWED = 3;

    public const FAV_ICON      = '/favicon.ico';
    public const DEFAULT_REGEX = '[^/]+';

    /** supported method list */
    public const GET     = 'GET';
    public const POST    = 'POST';
    public const PUT     = 'PUT';
    public const PATCH   = 'PATCH';
    public const DELETE  = 'DELETE';
    public const OPTIONS = 'OPTIONS';
    public const HEAD    = 'HEAD';

    public const COPY    = 'COPY';
    public const PURGE   = 'PURGE';
    public const LINK    = 'LINK';
    public const UNLINK  = 'UNLINK';
    public const LOCK    = 'LOCK';
    public const UNLOCK  = 'UNLOCK';
    public const SEARCH  = 'SEARCH';
    public const CONNECT = 'CONNECT';
    public const TRACE   = 'TRACE';

    /** supported methods name list */
    public const METHODS_ARRAY = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
        'HEAD',
        'CONNECT'
        // 'COPY', 'PURGE', 'LINK', 'UNLINK', 'LOCK', 'UNLOCK', 'VIEW', 'SEARCH', 'TRACE',
    ];

    // ,COPY,PURGE,LINK,UNLINK,LOCK,UNLOCK,VIEW,SEARCH,TRACE';
    /** supported methods name string */
    public const METHODS_STRING = ',GET,POST,PUT,PATCH,DELETE,OPTIONS,HEAD,CONNECT,';

    /** the matched result index key */
    public const INDEX_STATUS = 0;
    public const INDEX_PATH   = 1;
    public const INDEX_INFO   = 2;

    /**
     * add a route to the router.
     * @param string $method Request method name. eg 'GET'
     * @param string $path The route path. eg '/users'
     * @param mixed  $handler The route handler. allow: string, array, object
     * @param array  $binds The route path var bind. eg. [ 'id' => '[0-9]+', ]
     * @param array  $opts Extra options
     * - name: string
     * - ... more
     * @return Route
     */
    public function add(string $method, string $path, $handler, array $binds = [], array $opts = []): Route;

    /**
     * add a Route to the router
     * @param Route $route
     * @return Route
     */
    public function addRoute(Route $route): Route;

    /**
     * @param array|string    $methods The match request method(s). e.g ['get','post']
     * @param string          $path The route path string. is allow empty string. eg: '/user/login'
     * @param callable|string $handler
     * @param array           $binds route path var bind. eg. [ 'id' => '[0-9]+', ]
     * @param array           $opts some option data
     * [
     *     'defaults' => [ 'id' => 10, ],
     *     'domains'  => [ 'a-domain.com', '*.b-domain.com'],
     *     'schemas' => ['https'],
     * ]
     */
    public function map($methods, string $path, $handler, array $binds = [], array $opts = []);

    /**
     * find the matched route info for the given request uri path
     * @param string $method
     * @param string $path
     * @return array
     *
     *  [self::NOT_FOUND, $path, null]
     *  [self::METHOD_NOT_ALLOWED, $path, ['GET', 'OTHER_METHODS_ARRAY']]
     *  [self::FOUND, $path, array () // routeData ]
     *
     */
    public function match(string $path, string $method = 'GET'): array;

    /**
     * @return array
     */
    public function getChains(): array;
}
