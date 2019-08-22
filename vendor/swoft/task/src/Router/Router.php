<?php declare(strict_types=1);


namespace Swoft\Task\Router;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Task\Contract\RouterInterface;

/**
 * Class Router
 *
 * @since 2.0
 *
 * @Bean("taskRouter")
 */
class Router implements RouterInterface
{
    /**
     * @var array
     *
     * @example
     * [
     *    'taskName@mappingName' => $className
     * ]
     */
    private $routes = [];

    /**
     * @param string $className
     * @param string $taskName
     * @param string $mappingName
     * @param string $methodName
     */
    public function addRoute(string $className, string $taskName, string $mappingName, string $methodName): void
    {
        $route = $this->getRoute($taskName, $mappingName);

        $this->routes[$route] = [$className, $methodName];
    }

    /**
     * @param string $taskName
     * @param string $mappingName
     *
     * @return array
     */
    public function match(string $taskName, string $mappingName): array
    {
        $route = $this->getRoute($taskName, $mappingName);

        if (isset($this->routes[$route])) {
            return [self::FOUND, $this->routes[$route]];
        }

        return [self::NOT_FOUND, []];
    }

    /**
     * @param string $taskName
     * @param string $mappingName
     *
     * @return string
     */
    private function getRoute(string $taskName, string $mappingName): string
    {
        return sprintf('%s@%s', $taskName, $mappingName);
    }
}