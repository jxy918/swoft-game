<?php declare(strict_types=1);

namespace Swoft\Stdlib\Helper;

use function stripos;
use const PHP_OS;
use const PHP_SAPI;

/**
 * Env helper
 *
 * @since 2.0
 */
class EnvHelper
{
    /**
     * is Cli env
     *
     * @return  boolean
     */
    public static function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * is phpdbg env
     *
     * @return  boolean
     */
    public static function isPhpDbg(): bool
    {
        return PHP_SAPI === 'phpdbg';
    }

    /**
     * is windows OS
     *
     * @return bool
     */
    public static function isWin(): bool
    {
        return stripos(PHP_OS, 'WIN') === 0;
    }

    /**
     * is windows OS
     *
     * @return bool
     */
    public static function isWindows(): bool
    {
        return stripos(PHP_OS, 'WIN') === 0;
    }

    /**
     * is mac os
     *
     * @return bool
     */
    public static function isMac(): bool
    {
        return stripos(PHP_OS, 'Darwin') !== false;
    }
}
