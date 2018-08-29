<?php

namespace Swoft\Console\Router;

use Swoft\Http\Message\Router\HandlerMappingInterface;

/**
 * Handler mapping
 */
class HandlerMapping implements HandlerMappingInterface
{
    /**
     * default commands
     */
    const DEFAULT_METHODS = [
        'start',
        'reload',
        'stop',
        'restart',
    ];

    /**
     * @var string
     */
    private $suffix = 'Command';

    /**
     * the default group of command
     */
    private $defaultGroup = 'server';

    /**
     * the default command
     */
    private $defaultCommand = 'index';

    /**
     * the delimiter
     *
     * @var string
     */
    private $delimiter = ':';


    /**
     * @var array
     */
    private $routes = [];

    /**
     * @param array ...$params
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getHandler(...$params): array
    {
        list($group, $command) = $this->getGroupAndCommand();
        $route = $this->getCommandString($group, $command);

        return $this->match($route);
    }

    /**
     * Auto register routes
     *
     * @param array $commandMapping
     */
    public function register(array $commandMapping)
    {
        foreach ($commandMapping as $className => $mapping) {
            $prefix = $mapping['name'];
            $routes = $mapping['routes'];
            $coroutine = $mapping['coroutine'];
            $server = $mapping['server'];
            $prefix = $this->getPrefix($prefix, $className);
            $this->registerRoute($className, $routes, $prefix, $coroutine, $server);
        }
    }

    /**
     * @param string $comamnd
     * @return bool
     */
    public function isDefaultCommand(string $comamnd): bool
    {
        return $comamnd === $this->defaultCommand;
    }

    /**
     * @return array
     */
    private function getGroupAndCommand(): array
    {
        $cmd = input()->getCommand();
        if (\in_array($cmd, self::DEFAULT_METHODS, true)) {
            return [$this->defaultGroup, $cmd];
        }

        $commandAry = explode($this->delimiter, $cmd);
        if (\count($commandAry) >= 2) {
            list($group, $command) = $commandAry;
        } else {
            list($group) = $commandAry;
            $command = '';
        }

        if (empty($group)) {
            return [];
        }

        if (empty($command)) {
            $command = $this->defaultCommand;
        }

        return [$group, $command];
    }

    /**
     * Match route
     *
     * @param $route
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function match(string $route): array
    {
        if (!isset($this->routes[$route])) {
            return [];
        }

        return $this->routes[$route];
    }

    /**
     * Register one route
     *
     * @param string $className
     * @param array  $routes
     * @param string $prefix
     * @param bool   $coroutine
     * @param bool   $server
     */
    private function registerRoute(string $className, array $routes, string $prefix, bool $coroutine, $server)
    {
        foreach ($routes as $route) {
            $mappedName = $route['mappedName'];
            $methodName = $route['methodName'];
            if (empty($mappedName)) {
                $mappedName = $methodName;
            }

            $commandKey = $this->getCommandString($prefix, $mappedName);
            $this->routes[$commandKey] = [$className, $methodName, $coroutine, $server];
        }

        $commandKey = $this->getCommandString($prefix, $this->defaultCommand);
        $this->routes[$commandKey] = [$className, $this->defaultCommand];
    }

    /**
     * Get command from class name
     *
     * @param string $prefix
     * @param string $className
     * @return string
     */
    public function getPrefix(string $prefix, string $className): string
    {
        // the  prefix of annotation is exist
        if (! empty($prefix)) {
            return $prefix;
        }

        // the prefix of annotation is empty
        $reg = '/^.*\\\(\w+)' . $this->suffix . '$/';
        $prefix = '';

        if ($result = preg_match($reg, $className, $match)) {
            $prefix = lcfirst($match[1]);
        }

        return $prefix;
    }

    /**
     * @param string $group
     * @param string $command
     * @return string
     */
    private function getCommandString(string $group, string $command): string
    {
        return sprintf('%s%s%s', $group, $this->delimiter, $command);
    }
}
