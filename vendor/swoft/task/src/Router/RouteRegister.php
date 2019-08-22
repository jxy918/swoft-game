<?php declare(strict_types=1);


namespace Swoft\Task\Router;


class RouteRegister
{
    /**
     * @var array
     *
     * @example
     * [
     *     'className' => [
     *          'name' => 'taskName',
     *          'mapping' => [
     *             'methodName' => [
     *                  'name' => 'mappingName',
     *                  'method' => 'methodName',
     *              ]
     *          ]
     *      ]
     * ]
     */
    private static $tasks = [];

    /**
     * @param string $className
     * @param string $taskName
     */
    public static function registerByClassName(string $className, string $taskName): void
    {
        self::$tasks[$className]['name'] = $taskName;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param string $mappingName
     */
    public static function registerByMethodName(string $className, string $methodName, string $mappingName): void
    {
        // Fix empty name
        if (empty($mappingName)) {
            $mappingName = $methodName;
        }

        self::$tasks[$className]['mapping'][$methodName] = [
            'name'   => $mappingName,
            'method' => $methodName
        ];
    }

    /**
     * @param Router $router
     */
    public static function registerRoutes(Router $router): void
    {
        foreach (self::$tasks as $className => $task) {
            $name    = $task['name'] ?? '';
            $mapping = $task['mapping'] ?? [];

            if (empty($name) || empty($mapping)) {
                continue;
            }

            foreach ($mapping as $methodName => $map) {

                $mappingName = $map['name'] ?? '';
                if (empty($mappingName)) {
                    continue;
                }

                $router->addRoute($className, $name, $mappingName, $methodName);
            }
        }
    }
}