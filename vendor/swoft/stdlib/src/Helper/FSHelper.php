<?php

namespace Swoft\Stdlib\Helper;

use function array_pop;
use function explode;
use function implode;
use function preg_match;
use function preg_replace;
use function str_replace;
use function strpos;
use function substr;
use const DIRECTORY_SEPARATOR;

/**
 * Class FSHelper - file system helper
 *
 * @since 2.0
 */
class FSHelper
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function formatPath(string $path): string
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return str_replace('\\', '/', $path);
        }

        return $path;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public static function isAbsPath(string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (strpos($path, '/') === 0 // linux/mac
            || 1 === preg_match('#^[a-z]:[\/|\\\]{1}.+#i', $path) // windows
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $path e.g phar://E:/workenv/xxx/yyy/app.phar/web
     *
     * @return string
     */
    public function clearPharPath(string $path): string
    {
        if (strpos($path, 'phar://') === 0) {
            $path = (string)substr($path, 7);

            if (strpos($path, '.phar')) {
                return preg_replace('//[\w-]+\.phar/', '', $path);
            }
        }

        return $path;
    }

    /**
     * Returns canonicalized absolute pathname
     * Convert 'this/is/../a/./test/.///is' to 'this/a/test/is'
     *
     * @param string $path
     * @param bool   $filter
     *
     * @return string
     */
    public static function conv2abs(string $path, bool $filter = true): string
    {
        $path = str_replace('\\', '/', $path);

        if (strpos($path, '..') === false) {
            return $path;
        }

        $first = '';
        $parts = explode('/', $path);
        if ($filter) {
            $first = $path[0] === '/' ? '/' : '';
            $parts = array_filter($parts, 'strlen');
        }

        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' === $part) {
                continue;
            }

            if ('..' === $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return $first . implode('/', $absolutes);
    }
}
