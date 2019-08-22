<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Router;

/**
 * Class RouteRegister
 *
 * @since 2.0
 */
class RouteRegister
{
    /**
     * @var array
     *
     * @example
     * [
     *    'interface' => [
     *         'version' => 'className'
     *         'version2' => 'className2'
     *     ]
     * ]
     */
    private static $services = [];

    /**
     * @var array
     *
     * @example
     * [
     *      'className' => [
     *          'interface',
     *          'interface',
     *      ]
     * ]
     */
    private static $serviceClassNames = [];

    /**
     * @param string $interface
     * @param string $version
     * @param string $className
     */
    public static function register(string $interface, string $version, string $className): void
    {
        self::$services[$interface][$version] = $className;

        // Record classNames
        self::$serviceClassNames[$className] = $interface;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    public static function hasRouteByClassName(string $className): bool
    {
        return isset(self::$serviceClassNames[$className]);
    }

    /**
     * @param Router $router
     */
    public static function registerRoutes(Router $router): void
    {
        foreach (self::$services as $interface => $service) {
            foreach ($service as $version => $className) {
                $router->addRoute($interface, $version, $className);
            }
        }
    }
}