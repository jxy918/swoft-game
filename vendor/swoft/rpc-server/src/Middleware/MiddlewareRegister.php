<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Middleware;

use function array_unique;
use Swoft\Rpc\Server\Exception\RpcServerException;
use Swoft\Rpc\Server\Router\RouteRegister;

/**
 * Class MiddlewareRegister
 *
 * @since 2.0
 */
class MiddlewareRegister
{
    /**
     * All Middlewares within class and method
     *
     * @var array
     *
     * @example
     * [
     *     'className' => [
     *          'class' => [
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
     * Register class middleware
     *
     * @param string $name      middleware name
     * @param string $className class name
     *
     * @return void
     */
    public static function registerByClassName(string $name, string $className): void
    {
        $middlewares   = self::$middlewares[$className]['class'] ?? [];
        $middlewares[] = $name;

        self::$middlewares[$className]['class'] = array_unique($middlewares);
    }

    /**
     * Register method middleware
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
     */
    public static function register(): void
    {
        foreach (self::$middlewares as $className => $middlewares) {
            // `@Service` is undefined on class
            if (!RouteRegister::hasRouteByClassName($className)) {
                throw new RpcServerException(sprintf('`@Service` is undefined on class(%s)', $className));
            }

            $classMiddlewares  = $middlewares['class'] ?? [];
            $methodMiddlewares = $middlewares['methods'] ?? [];

            foreach ($methodMiddlewares as $methodName => $oneMethodMiddlewares) {
                if (!empty($oneMethodMiddlewares)) {
                    $allMiddlewares = array_merge($classMiddlewares, $oneMethodMiddlewares);

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

        $middlewares = self::$middlewares[$className]['class'] ?? [];
        return $middlewares;
    }
}