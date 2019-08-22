<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Router;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Contract\RouterInterface;
use Swoft\Stdlib\Helper\Str;
use function array_shift;
use function count;
use function preg_match;
use function preg_match_all;
use function strpos;
use function strtr;
use function trim;

/**
 * Class Router
 *
 * @package Swoft\WebSocket\Server\Router
 *
 * @Bean("wsRouter")
 */
class Router implements RouterInterface
{
    // Default var regex
    public const DEFAULT_REGEX = '[^/]+';

    /**
     * Command counter
     *
     * @var int
     */
    private $counter = 0;

    /**
     * WebSocket modules
     *
     * [
     *  '/echo' => [
     *      'path'   => route path,
     *      'class'  => moduleClass,
     *      'name'   => module name,
     *      'params' => ['id' => '\d+'],
     *      'messageParser'  => message parser class,
     *      'defaultOpcode'  => 1,
     *      'defaultCommand' => default command,
     *      'eventMethods'   => [
     *          'handshake' => method1, (on the moduleClass)
     *          'open'      => method2,
     *          'close'     => method3,
     *      ],
     *  ],
     *  ... ...
     * ]
     *
     * @var array
     */
    private $modules = [];

    /**
     * Message commands for each module
     *
     * [
     *  '/echo' => [
     *      'prefix1.cmd1' => [
     *          'opcode'  => 0,
     *          'handler' => [controllerClass1, method1],
     *      ],
     *  ],
     * ]
     *
     * @var array
     */
    private $commands = [];

    /**
     * Want disabled modules
     *
     * [
     *  // path => 1,
     *  '/echo' => 1,
     * ]
     *
     * @var array
     */
    private $disabledModules = [];

    /**
     * @param string $path
     * @param array  $info module Info
     */
    public function addModule(string $path, array $info = []): void
    {
        $path = Str::formatPath($path);

        // It's an disabled module
        if (isset($this->disabledModules[$path])) {
            return;
        }

        // Re-set path
        $info['path']  = $path;
        $info['regex'] = '';

        // Not exist path var. eg: "/users/{id}"
        if (strpos($path, '{') === false) {
            $this->modules[$path] = $info;
            return;
        }

        $matches = [];
        $params  = $info['params'] ?? [];

        // Parse the parameters and replace them with the corresponding regular
        if (preg_match_all('#\{([a-zA-Z_][\w-]*)\}#', $path, $matches)) {
            /** @var array[] $m */
            $pairs = [];

            foreach ($matches[1] as $name) {
                $regex = $params[$name] ?? self::DEFAULT_REGEX;
                // Build pairs
                $pairs['{' . $name . '}'] = '(' . $regex . ')';
            }

            $info['vars']  = $matches[1];
            $info['regex'] = '#^' . strtr($path, $pairs) . '$#';
        }

        $this->modules[$path] = $info;
    }

    /**
     * @param string   $path
     * @param string   $cmdId
     * @param callable $handler
     * @param array    $info
     */
    public function addCommand(string $path, string $cmdId, $handler, array $info = []): void
    {
        $path = Str::formatPath($path);

        // It's an disabled module
        if (isset($this->disabledModules[$path])) {
            return;
        }

        // Set handler
        $info['cmdId']   = $cmdId;
        $info['handler'] = $handler;

        $this->counter++;
        $this->commands[$path][$cmdId] = $info;
    }

    /**
     * Match route path for find module info
     *
     * @param string $path e.g '/echo'
     *
     * @return array
     */
    public function match(string $path): array
    {
        $path = Str::formatPath($path);
        if (isset($this->modules[$path])) {
            return $this->modules[$path];
        }

        // If is dynamic route
        foreach ($this->modules as $module) {
            if (!$pathRegex = $module['regex']) {
                continue;
            }

            // Regex match
            $matches = [];
            if (preg_match($pathRegex, $path, $matches)) {
                $params   = [];
                $pathVars = $module['vars'];

                // First is full match.
                array_shift($matches);
                foreach ($matches as $index => $value) {
                    $params[$pathVars[$index]] = $value;
                }

                $module['routeParams'] = $params;
                return $module;
            }
        }

        return [];
    }

    /**
     * @param string $path
     * @param string $route The message route command ID. like 'home.index'
     *
     * @return array Return match result
     *                      [
     *                          status,
     *                          route info
     *                      ]
     */
    public function matchCommand(string $path, string $route): array
    {
        $path = Str::formatPath($path);
        if (!isset($this->commands[$path])) {
            return [self::NOT_FOUND, null];
        }

        $route = trim($route) ?: $this->modules[$path]['defaultCommand'];

        if (isset($this->commands[$path][$route])) {
            return [self::FOUND, $this->commands[$path][$route]];
        }

        return [self::NOT_FOUND, null];
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function hasModule(string $path): bool
    {
        return isset($this->modules[$path]);
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @return int
     */
    public function getModuleCount(): int
    {
        return count($this->modules);
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    /**
     * @return array
     */
    public function getDisabledModules(): array
    {
        return $this->disabledModules;
    }

    /**
     * @param array $paths
     */
    public function setDisabledModules(array $paths): void
    {
        foreach ($paths as $path) {
            $this->disabledModules[$path] = 1;
        }
    }
}
