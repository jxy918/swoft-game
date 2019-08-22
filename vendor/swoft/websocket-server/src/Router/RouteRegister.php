<?php

namespace Swoft\WebSocket\Server\Router;

use Swoft\Stdlib\Helper\Str;
use function array_merge;

/**
 * Class RouteRegister
 *
 * @since 2.0
 */
final class RouteRegister
{
    /**
     * @var array
     * [
     *  module class => [
     *      path => '/chat/{id}',
     *      params => ['id' => '\d+'],
     *      controllers => ['class1', 'class2'],
     *  ]
     * ]
     */
    private static $modules = [];

    /**
     * @var array
     * [
     *  controller class => [
     *      prefix => 'home',
     *
     *  ]
     * ]
     */
    private static $commands = [];

    /**
     * @param string $class
     * @param array  $option
     */
    public static function bindModule(string $class, array $option): void
    {
        if (isset(self::$modules[$class])) {
            self::$modules[$class] = array_merge(self::$modules[$class], $option);
        } else {
            self::$modules[$class] = $option;
        }
    }

    /**
     * @param string $moduleClass
     * @param string $method
     * @param string $event such as: message, handshake, open, close
     */
    public static function bindEvent(string $moduleClass, string $method, string $event): void
    {
        self::$modules[$moduleClass]['eventMethods'][$event] = $method;
    }

    /**
     * @param string $class
     * @param string $prefix
     */
    public static function bindController(string $class, string $prefix): void
    {
        self::$commands[$class] = [
            'prefix' => $prefix ?: Str::getClassName($class, 'Controller'),
            'class'  => $class,
            'routes' => [], // see bindCommand()
        ];
    }

    /**
     * @param string $class
     * @param string $method
     * @param string $command
     * @param array  $options
     */
    public static function bindCommand(string $class, string $method, string $command, array $options = []): void
    {
        $options['method']  = $method;
        $options['command'] = $command ?: $method;

        self::$commands[$class]['routes'][] = $options;
    }

    /**
     * @param Router $router
     */
    public static function registerTo(Router $router): void
    {
        // Modules
        foreach (self::$modules as $mdlClass => $mdlInfo) {
            $path = $mdlInfo['path'];
            $router->addModule($path, $mdlInfo);

            // Commands
            foreach ($mdlInfo['controllers'] as $ctrlClass) {
                if (!isset(self::$commands[$ctrlClass])) {
                    continue;
                }

                $info   = self::$commands[$ctrlClass];
                $prefix = $info['prefix'];
                // save module class
                $info['module'] = $mdlClass;

                foreach ($info['routes'] as $route) {
                    $cmd   = $route['command'];
                    $cmdId = $route['isRoot'] ? $cmd : $prefix . '.' . $cmd;

                    $router->addCommand($path, $cmdId, [$ctrlClass, $route['method']], [
                        'opcode' => $route['opcode'] ?: $mdlInfo['defaultOpcode'],
                    ]);
                }
            }
        }

        // Clear data
        self::$commands = self::$modules = [];
    }
}
