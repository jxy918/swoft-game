<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/4/19 0019
 * Time: 23:56
 */

namespace Swoft\Http\Server\Helper;

use function preg_replace;
use function rawurldecode;
use function rtrim;
use function strpos;

/**
 * Class RouteHelper
 * @package Swoft\Http\Server\Helper
 */
class RouteHelper
{
    /**
     * check route path is static route
     * @param string $route
     * @return bool
     */
    public static function isStaticRoute(string $route): bool
    {
        return strpos($route, '{') === false && strpos($route, '[') === false;
    }

    /**
     * Format URI path
     * @param string $path
     * @param bool   $ignoreLastSlash
     * @return string
     */
    public static function formatPath(string $path, bool $ignoreLastSlash = true): string
    {
        if ($path === '/') {
            return '/';
        }

        // Clear '//', '///' => '/'
        if (false !== strpos($path, '//')) {
            $path = preg_replace('#\/\/+#', '/', $path);
        }

        // Must be start withs '/'
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        // Decode
        $path = rawurldecode($path);

        return $ignoreLastSlash ? rtrim($path, '/') : $path;
    }
}
