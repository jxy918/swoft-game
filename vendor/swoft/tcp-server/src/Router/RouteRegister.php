<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Router;

use Swoft\Stdlib\Helper\Str;
use Swoft\Tcp\Server\Exception\TcpServerRouteException;

/**
 * Class RouteRegister
 *
 * @since 2.0.3
 */
final class RouteRegister
{
    /**
     * @var array
     * [
     *  controller class => [
     *      prefix => 'home',
     *      route => 'index',
     *      root  => false,
     *  ]
     * ]
     */
    private static $routes = [];

    /**
     * @param string $controllerClass
     * @param string $prefix
     */
    public static function bindController(string $controllerClass, string $prefix): void
    {
        self::$routes[$controllerClass] = [
            'prefix' => $prefix ?: Str::getClassName($controllerClass, 'Controller'),
            'class'  => $controllerClass,
            'routes' => [], // see bindCommand()
        ];
    }

    /**
     * @param string $controllerClass
     * @param string $method
     * @param array  $info
     */
    public static function bindCommand(string $controllerClass, string $method, array $info): void
    {
        $info['method'] = $method;

        self::$routes[$controllerClass]['routes'][] = $info;
    }

    /**
     * @param Router $router
     *
     * @throws TcpServerRouteException
     */
    public static function registerTo(Router $router): void
    {
        $delimiter = $router->getDelimiter();

        foreach (self::$routes as $ctrlClass => $group) {
            $prefix = $group['prefix'];

            // Register routes
            foreach ($group['routes'] as $route) {
                $path = $route['route'];

                // Is not root command route. prepend group prefix.
                if ($prefix && !$route['root']) {
                    $path = $prefix . $delimiter . $path;
                }

                $router->add($path, [$ctrlClass, $route['method']]);
            }
        }

        // Clear data
        self::$routes = [];
    }
}
