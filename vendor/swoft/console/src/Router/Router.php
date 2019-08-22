<?php declare(strict_types=1);

namespace Swoft\Console\Router;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Console\Contract\RouterInterface;
use function array_keys;
use function array_merge;
use function count;
use function explode;
use function in_array;
use function ksort;
use function sort;
use function sprintf;
use function strpos;
use function trim;

/**
 * Class Router
 *
 * @since 2.0
 * @Bean("cliRouter")
 */
class Router implements RouterInterface
{
    /**
     * @var string
     */
    private $suffix = 'Command';

    /**
     * The default group of command. eg: http
     *
     * @var string
     */
    private $defaultGroup = '';

    /**
     * The default command. eg. 'start'
     *
     * @var string
     */
    private $defaultCommand = 'index';

    /**
     * The default commands. eg. ['start', 'stop']
     *
     * @var string[]
     */
    private $defaultCommands = [];

    /**
     * The delimiter for split group and command
     *
     * @var string
     */
    private $delimiter = ':';

    /**
     * Use for render help info panel
     *
     * @var int
     */
    private $keyWidth = 12;

    /**
     * All commands routes data
     *
     * @var array
     * [
     *  // route ID => route info.
     *  'group:cmd' => [
     *      'handler'  => [group class, command method],
     *      'metadata' => [
     *          'aliases'   => [],
     *          'options'   => [],
     *          'arguments' => [],
     *      ],
     *  ]
     * ]
     */
    private $routes = [];

    /**
     * Will record the relationship data of group and commands
     *
     * @var array
     * [
     *  group1 => [
     *      desc => 'description',
     *      class => Group class,
     *      names => [cmd1, cmd2],
     *      aliases => [],
     *  ],
     *  ...
     * ]
     */
    private $groups = [];

    /**
     * @var array
     * [
     *   alias  => command ID(group:command),
     *  'start' => 'http:start'
     * ]
     */
    private $idAliases = [];

    /**
     * @var array [alias => real name]
     */
    private $groupAliases = [];

    /**
     * @var array [alias => real name]
     */
    private $commandAliases = [];

    /**
     * @var array [real name => 1]
     */
    private $disabledGroups = [];

    /**
     * @param string         $group
     * @param string         $command
     * @param mixed|callable $handler
     * @param array          $config
     */
    public function map(string $group, string $command, $handler, array $config = []): void
    {
        // Filter disabled groups
        if (isset($this->disabledGroups[$group])) {
            return;
        }

        $cmdID = $this->buildCommandID($group, $command);

        if ($aliases = $config['aliases'] ?? []) {
            $this->setCommandAliases($command, $aliases);
        }

        $config['handler'] = $handler;

        $this->routes[$cmdID] = $config;
    }

    /**
     * Match route by input command
     *
     * @param array $params [$route]
     *
     * @return array
     *
     * [
     *  status, info(array)
     * ]
     */
    public function match(...$params): array
    {
        $delimiter = $this->delimiter;
        $inputCmd  = trim($params[0], "$delimiter ");
        $noSepChar = strpos($inputCmd, $delimiter) === false;

        // If use command ID alias
        if ($noSepChar && isset($this->idAliases[$inputCmd])) {
            $inputCmd = $this->idAliases[$inputCmd];
            // Must re-check
            $noSepChar = strpos($inputCmd, $delimiter) === false;
        }

        if ($noSepChar && in_array($inputCmd, $this->defaultCommands, true)) {
            $group   = $this->defaultGroup;
            $command = $this->resolveCommandAlias($inputCmd);

            // Only a group name
        } elseif ($noSepChar) {
            $group = $this->resolveGroupAlias($inputCmd);

            if (isset($this->groups[$group])) {
                return [self::ONLY_GROUP, ['group' => $group]];
            }

            return [self::NOT_FOUND];
        } else {
            $nameList = explode($delimiter, $inputCmd, 2);

            if (count($nameList) === 2) {
                [$group, $command] = $nameList;
                // resolve command alias
                $command = $this->resolveCommandAlias($command);
            } else {
                $command = '';
                // $command = $this->defaultCommand;
                $group = $nameList[0];
            }
        }

        $group = $this->resolveGroupAlias($group);
        // build command ID
        $commandID = $this->buildCommandID($group, $command);

        if (isset($this->routes[$commandID])) {
            $info = $this->routes[$commandID];
            // append some info
            $info['cmdId'] = $commandID;

            return [self::FOUND, $info];
        }

        if ($group && isset($this->groups[$group])) {
            return [self::ONLY_GROUP, ['group' => $group]];
        }

        return [self::NOT_FOUND];
    }

    /**
     * @param callable $grpFunc function(string $group, array $info)
     * @param callable $cmdFunc function(string $id, array $info)
     */
    public function sortedEach(callable $grpFunc, callable $cmdFunc = null): void
    {
        $groups = $this->groups;
        ksort($groups);

        foreach ($groups as $group => $info) {
            $names = $info['names'];
            sort($names);
            unset($info['names']);

            // call group handle func
            $grpFunc($group, $info);

            // not set cmd handler func
            if ($cmdFunc === null) {
                continue;
            }

            foreach ($names as $name) {
                $id = $this->buildCommandID($group, $name);
                // call command handle func
                $cmdFunc($id, $this->routes[$id]);
            }
        }
    }

    /**
     * @param string $cmdID It is equals to 'group:command'
     *
     * @return array
     */
    public function getRouteByID(string $cmdID): array
    {
        return $this->routes[$cmdID] ?? [];
    }

    /**
     * @param string $alias
     *
     * @return string
     */
    public function getGroupName(string $alias): string
    {
        return $this->groupAliases[$alias] ?? $alias;
    }

    /**
     * @param string $alias
     *
     * @return string
     */
    public function getCommandName(string $alias): string
    {
        return $this->commandAliases[$alias] ?? $alias;
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function isDefault(string $command): bool
    {
        return $command === $this->defaultCommand;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isGroup(string $name): bool
    {
        return isset($this->groups[$name]);
    }

    /**
     * command ID = group + : + command
     *
     * @param string $group
     * @param string $command
     *
     * @return string
     */
    public function buildCommandID(string $group, string $command): string
    {
        if ($group) {
            return sprintf('%s%s%s', $group, $this->delimiter, $command);
        }

        return $command;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function resolveGroupAlias(string $name): string
    {
        return $this->groupAliases[$name] ?? $name;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function resolveCommandAlias(string $name): string
    {
        return $this->commandAliases[$name] ?? $name;
    }

    /**
     * @param string $group
     * @param array  $aliases
     */
    public function setGroupAliases(string $group, array $aliases): void
    {
        foreach ($aliases as $alias) {
            $this->setGroupAlias($group, $alias);
        }
    }

    /**
     * @param string $group
     * @param string $alias
     */
    public function setGroupAlias(string $group, string $alias): void
    {
        if ($group && $alias) {
            // Filter disabled groups
            if (isset($this->disabledGroups[$group])) {
                return;
            }

            $this->groupAliases[$alias] = $group;
        }
    }

    /**
     * @param string $command
     * @param array  $aliases
     */
    public function setCommandAliases(string $command, array $aliases): void
    {
        foreach ($aliases as $alias) {
            $this->setCommandAlias($command, $alias);
        }
    }

    /**
     * @param string $command
     * @param string $alias
     */
    public function setCommandAlias(string $command, string $alias): void
    {
        if ($command && $alias) {
            $this->commandAliases[$alias] = $command;
        }
    }

    /**
     * Get all name for search
     *
     * @return array
     */
    public function getAllNames(): array
    {
        return array_merge(array_keys($this->getGroupAliases()), array_keys($this->groups));
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @return string
     */
    public function getDefaultCommand(): string
    {
        return $this->defaultCommand;
    }

    /**
     * @return array
     */
    public function getGroupAliases(): array
    {
        return $this->groupAliases;
    }

    /**
     * @return array
     */
    public function getCommandAliases(): array
    {
        return $this->commandAliases;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param string $name
     * @param array  $info
     */
    public function setGroupInfo(string $name, array $info): void
    {
        // Filter disabled groups
        if (isset($this->disabledGroups[$name])) {
           return;
        }

        $this->groups[$name] = $info;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getGroupInfo(string $name): array
    {
        return $this->groups[$name] ?? [];
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups): void
    {
        // Filter disabled groups
        foreach ($groups as $name => $info) {
            if (isset($this->disabledGroups[$name])) {
                unset($groups[$name]);
            }
        }

        $this->groups = $groups;
    }

    /**
     * @param int $plusValue
     *
     * @return int
     */
    public function getKeyWidth(int $plusValue = 0): int
    {
        return $this->keyWidth + $plusValue;
    }

    /**
     * @param int $keyWidth
     */
    public function setKeyWidth(int $keyWidth): void
    {
        $this->keyWidth = $keyWidth;
    }

    /**
     * @return int
     */
    public function groupCount(): int
    {
        return count($this->groups);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->routes);
    }

    /**
     * @return array
     */
    public function getIdAliases(): array
    {
        return $this->idAliases;
    }

    /**
     * @param array $idAliases
     */
    public function setIdAliases(array $idAliases): void
    {
        if ($idAliases) {
            $this->idAliases = array_merge($this->idAliases, $idAliases);
        }
    }

    /**
     * @return string[]
     */
    public function getDefaultCommands(): array
    {
        return $this->defaultCommands;
    }

    /**
     * @param array $defaultCommands
     */
    public function setDefaultCommands(array $defaultCommands): void
    {
        $this->defaultCommands = $defaultCommands;
    }

    /**
     * @return array
     */
    public function getDisabledGroups(): array
    {
        return $this->disabledGroups;
    }

    /**
     * @param array $groupNames
     */
    public function setDisabledGroups(array $groupNames): void
    {
        foreach ($groupNames as $name) {
            $this->disableGroup($name);
        }
    }

    /**
     * @param string $name
     */
    public function disableGroup(string $name): void
    {
        if ($name) {
            $this->disabledGroups[$name] = 1;
        }
    }
}
