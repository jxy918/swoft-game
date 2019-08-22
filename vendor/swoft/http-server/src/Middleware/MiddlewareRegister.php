<?php declare(strict_types=1);


namespace Swoft\Http\Server\Middleware;

use function array_unique;
use Swoft\Http\Server\Exception\HttpServerException;
use Swoft\Http\Server\Router\RouteRegister;

/**
 * Class MiddlewareRegister
 *
 * @since 2.0
 */
class MiddlewareRegister
{
    /**
     * All Middlewares within controller and action
     *
     * @var array
     *
     * @example
     * [
     *     'className' => [
     *          'controller' => [
     *              'middlewareName',
     *              'middlewareName',
     *              'middlewareName'
     *          ]
     *          'methods' => [
     *              'actionName' => [
     *                  'middlewareName',
     *                  'middlewareName',
     *                  'middlewareName'
     *              ]
     *          ]
     *     ]
     * ]
     */
    private static $middlewares = [];

    /**
     * @var array
     *
     * @example
     * [
     *      'className' => [
     *          'methodName' => [
     *              'middlewareName',
     *              'middlewareName',
     *          ]
     *      ]
     * ]
     */
    private static $handlerMiddlewares = [];

    /**
     * Register controller middleware
     *
     * @param string $name      middleware name
     * @param string $className class name
     *
     * @return void
     */
    public static function registerByClassName(string $name, string $className): void
    {
        $middlewares   = self::$middlewares[$className]['controller'] ?? [];
        $middlewares[] = $name;

        self::$middlewares[$className]['controller'] = array_unique($middlewares);
    }

    /**
     * Register action middleware
     *
     * @param string $name
     * @param string $className
     * @param string $methodName
     *
     * @return void
     */
    public static function registerByMethodName(string $name, string $className, string $methodName): void
    {
        $middlewares   = self::$middlewares[$className]['methods'][$methodName] ?? [];
        $middlewares[] = $name;

        self::$middlewares[$className]['methods'][$methodName] = array_unique($middlewares);
    }

    /**
     * Register handler middleware
     * @throws HttpServerException
     */
    public static function register(): void
    {
        foreach (self::$middlewares as $className => $middlewares) {
            // `@Controller` is undefined
            if (!RouteRegister::hasRouteByClassName($className)) {
                throw new HttpServerException(sprintf('`@Controller` is undefined on class(%s)', $className));
            }

            $controllerMiddlewares = $middlewares['controller'] ?? [];
            $methodMiddlewares     = $middlewares['methods'] ?? [];

            foreach ($methodMiddlewares as $methodName => $oneMethodMiddlewares) {
                if (!empty($oneMethodMiddlewares) || !empty($oneMethodMiddlewares)) {
                    $allMiddlewares = array_merge($controllerMiddlewares, $oneMethodMiddlewares);

                    self::$handlerMiddlewares[$className][$methodName] = array_unique($allMiddlewares);
                }
            }
        }
    }

    /**
     * @param string $className
     * @param string $methodName
     *
     * @return array
     */
    public static function getMiddlewares(string $className, string $methodName): array
    {
        $middlewares = self::$handlerMiddlewares[$className][$methodName] ?? [];
        if (!empty($middlewares)) {
            return $middlewares;
        }

        $middlewares = self::$middlewares[$className]['controller'] ?? [];
        return $middlewares;
    }
}