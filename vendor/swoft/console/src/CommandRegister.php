<?php declare(strict_types=1);

namespace Swoft\Console;

use ReflectionException;
use Swoft;
use Swoft\Annotation\Exception\AnnotationException;
use Swoft\Console\Helper\DocBlock;
use Swoft\Console\Router\Router;
use Swoft\Stdlib\Helper\Str;
use function array_merge;
use function strlen;
use function ucfirst;

/**
 * Class CommandRegister
 * @since 2.0
 */
final class CommandRegister
{
    /**
     * @var array[]
     * [
     *  class => [
     *      group   => group name,
     *      desc    => group description,
     *      alias   => group alias string,
     *      aliases => [], // group alias list
     *      options => [], // group options, will apply for all commands
     *      commands => [
     *          method => [
     *              name      => command name,
     *              desc      => command description,
     *              alias     => command alias string,
     *              options   => [],
     *              arguments => [],
     *          ]
     *      ]
     *  ]
     * ]
     */
    private static $commands = [];

    /**
     * @param string $class
     * @param string $group
     * @param array  $info
     */
    public static function addGroup(string $class, string $group, array $info): void
    {
        $info['group'] = $group;
        // save
        self::$commands[$class] = $info;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array  $route
     *
     * @throws AnnotationException
     */
    public static function addRoute(string $class, string $method, array $route): void
    {
        self::checkClass($class);

        // init some keys
        $route['options']   = [];
        $route['arguments'] = [];
        // save
        self::$commands[$class]['commands'][$method] = $route;
    }

    /**
     * @param string $class
     * @param string $method
     * @param string $argName
     * @param array  $info
     */
    public static function bindArgument(string $class, string $method, string $argName, array $info): void
    {
        $cmdInfo = self::$commands[$class]['commands'][$method];
        // add argument info
        $cmdInfo['arguments'][$argName] = $info;
        // re-setting
        self::$commands[$class]['commands'][$method] = $cmdInfo;
    }

    /**
     * @param string $class
     * @param string $method
     * @param string $optName
     * @param array  $info
     */
    public static function bindOption(string $class, string $method, string $optName, array $info): void
    {
        // if not 'commands', is bind group options.
        if (!isset(self::$commands[$class]['commands'])) {
            self::$commands[$class]['options'][$optName] = $info;
            return;
        }

        $cmdInfo = self::$commands[$class]['commands'][$method];
        // add option info
        $cmdInfo['options'][$optName] = $info;
        // re-setting
        self::$commands[$class]['commands'][$method] = $cmdInfo;
    }

    /**
     * @param string $class
     *
     * @throws AnnotationException
     */
    private static function checkClass(string $class): void
    {
        if (!isset(self::$commands[$class])) {
            throw new AnnotationException(
                "The class '{$class}' must add @Command on use annotation @CommandXXX"
            );
        }
    }

    /**
     * @param Router $router
     * @throws ReflectionException
     */
    public static function register(Router $router): void
    {
        $maxLen  = 12;
        $groups  = [];
        $docOpts = [
            'allow' => ['example']
        ];
        $defInfo = [
            'example'     => '',
            'description' => 'No description message',
        ];

        foreach (self::$commands as $class => $mapping) {
            $names = [];
            $group = $mapping['group'];
            // Set ID aliases
            $router->setIdAliases($mapping['idAliases']);
            // Set group name aliases
            $router->setGroupAliases($group, $mapping['aliases']);

            $refInfo = Swoft::getReflection($class);
            $mhdInfo = $refInfo['methods'] ?? [];
            $grpOpts = $mapping['options'] ?? [];

            foreach ($mapping['commands'] as $method => $route) {
                // $method = $route['method'];
                $cmdDesc = $route['desc'];
                $command = $route['command'];

                $idLen = strlen($group . $command);
                if ($idLen > $maxLen) {
                    $maxLen = $idLen;
                }

                $cmdExam = '';
                if (!empty($mhdInfo[$method]['comments'])) {
                    $tagInfo = DocBlock::getTags($mhdInfo[$method]['comments'], $docOpts, $defInfo);
                    $cmdDesc = $cmdDesc ?: Str::firstLine($tagInfo['description']);
                    $cmdExam = $tagInfo['example'];
                }

                $route['group']   = $group;
                $route['desc']    = ucfirst($cmdDesc);
                $route['example'] = $cmdExam;
                $route['options'] = self::mergeOptions($grpOpts, $route['options']);
                // Append group option
                $route['enabled']   = $mapping['enabled'];
                $route['coroutine'] = $mapping['coroutine'];

                $router->map($group, $command, [$class, $method], $route);
                $names[] = $command;
            }

            $groupExam = '';
            $groupDesc = $mapping['desc'];
            if (!empty($refInfo['comments'])) {
                $tagInfo   = DocBlock::getTags($refInfo['comments'], $docOpts, $defInfo);
                $groupDesc = $groupDesc ?: Str::firstLine($tagInfo['description']);
                $groupExam = $tagInfo['example'];
            }

            $groups[$group] = [
                'names'   => $names,
                'desc'    => ucfirst($groupDesc),
                'class'   => $class,
                'alias'   => $mapping['alias'],
                'aliases' => $mapping['aliases'],
                'example' => $groupExam,
            ];
        }

        $router->setGroups($groups);
        // +1 because router->delimiter
        $router->setKeyWidth($maxLen + 1);
        // clear data
        self::$commands = [];
    }

    /**
     * @param array $grpOptions
     * @param array $cmdOptions
     * @return array
     */
    private static function mergeOptions(array $grpOptions, array $cmdOptions): array
    {
        if ($grpOptions) {
            return array_merge($grpOptions, $cmdOptions);
        }

        return $cmdOptions;
    }
}
