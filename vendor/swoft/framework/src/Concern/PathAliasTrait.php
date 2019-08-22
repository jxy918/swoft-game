<?php

namespace Swoft\Concern;

use function explode;
use InvalidArgumentException;
use function str_replace;

/**
 * Trait PathAliasTrait
 * @since 2.0
 */
trait PathAliasTrait
{
    /**
     * Aliases
     *
     * @var array
     */
    private static $aliases = [];

    /**
     * Register multi aliases
     *
     * @param array $aliases
     */
    public static function setAliases(array $aliases): void
    {
        foreach ($aliases as $name => $path) {
            self::setAlias($name, $path);
        }
    }

    /**
     * Register alias
     *
     * @param string $alias
     * @param string $path
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public static function setAlias(string $alias, string $path = ''): void
    {
        if ($alias[0] !== '@') {
            $alias = '@' . $alias;
        }

        // Delete alias
        if (!$path) {
            unset(self::$aliases[$alias]);
            return;
        }

        // $path is not alias
        if ($path[0] !== '@') {
            self::$aliases[$alias] = $path;
            return;
        }

        // $path is alias
        if (isset(self::$aliases[$path])) {
            self::$aliases[$alias] = self::$aliases[$path];
            return;
        }

        [$root] = explode('/', $path);
        if (!isset(self::$aliases[$root])) {
            throw new InvalidArgumentException('The set root alias does not exist，alias=' . $root);
        }

        $rootPath  = self::$aliases[$root];
        $aliasPath = str_replace($root, '', $path);

        self::$aliases[$alias] = $rootPath . $aliasPath;
    }

    /**
     * Get alias real name
     *
     * @param string $alias
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getAlias(string $alias): string
    {
        // empty OR not an alias
        if (!$alias || $alias[0] !== '@') {
            return $alias;
        }

        if (isset(self::$aliases[$alias])) {
            return self::$aliases[$alias];
        }

        [$root] = explode('/', $alias, 2);
        if (!isset(self::$aliases[$root])) {
            throw new InvalidArgumentException('The set root alias does not exist，alias=' . $root);
        }

        $rootPath  = self::$aliases[$root];
        $aliasPath = str_replace($root, '', $alias);

        return $rootPath . $aliasPath;
    }

    /**
     * Whether the alias is exist
     *
     * @param string $alias
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function hasAlias(string $alias): bool
    {
        // empty OR not an alias
        if (!$alias || $alias[0] !== '@') {
            return false;
        }

        return isset(self::$aliases[$alias]);
    }

    /**
     * @return array
     */
    public static function getAliases(): array
    {
        return self::$aliases;
    }
}
