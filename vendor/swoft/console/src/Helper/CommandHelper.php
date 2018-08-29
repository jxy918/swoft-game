<?php

namespace Swoft\Console\Helper;

/**
 * Class CommandHelper
 * @package Swoft\Console\Helper
 */
class CommandHelper
{
    /**
     * These words will be as a Boolean value
     */
    const TRUE_WORDS = '|on|yes|true|';
    const FALSE_WORDS = '|off|no|false|';

    /**
     * Parses $GLOBALS['argv'] for parameters and assigns them to an array.
     * eg:
     * ```
     * php cli.php server start name=john city=chengdu -s=test --page=23 -d -rf --debug --task=off -y=false -D -e dev -v vvv
     * ```
     * ```php
     * $result = InputParser::fromArgv($_SERVER['argv']);
     * ```
     * Supports args:
     * <value>
     * arg=<value>
     * Supports opts:
     * -e
     * -e <value>
     * -e=<value>
     * --long-opt
     * --long-opt <value>
     * --long-opt=<value>
     * @link http://php.net/manual/zh/function.getopt.php#83414
     * @from inhere/console
     * @param array $params
     * @param array $config
     * @return array
     */
    public static function parse(array $params, array $config = []): array
    {
        $config = array_merge([
            // List of parameters without values(bool option keys)
            'noValues' => [], // ['debug', 'h']
            // Whether merge short-opts and long-opts
            'mergeOpts' => false,
            // list of params allow array.
            'arrayValues' => [], // ['names', 'status']
        ], $config);

        $args = $sOpts = $lOpts = [];
        $noValues = array_flip((array)$config['noValues']);
        $arrayValues = array_flip((array)$config['arrayValues']);

        // each() will deprecated at 7.2. so,there use current and next instead it.
        // while (list(,$p) = each($params)) {
        while (false !== ($p = current($params))) {
            next($params);

            // is options
            if ($p{0} === '-') {
                $val = true;
                $opt = substr($p, 1);
                $isLong = false;

                // long-opt: (--<opt>)
                if ($opt{0} === '-') {
                    $opt = substr($opt, 1);
                    $isLong = true;

                    // long-opt: value specified inline (--<opt>=<value>)
                    if (strpos($opt, '=') !== false) {
                        list($opt, $val) = explode('=', $opt, 2);
                    }

                    // short-opt: value specified inline (-<opt>=<value>)
                } elseif (isset($opt{1}) && $opt{1} === '=') {
                    list($opt, $val) = explode('=', $opt, 2);
                }

                // check if next parameter is a descriptor or a value
                $nxt = current($params);

                // next elem is value. fix: allow empty string ''
                if ($val === true && !isset($noValues[$opt]) && self::nextIsValue($nxt)) {
                    // list(,$val) = each($params);
                    $val = $nxt;
                    next($params);

                    // short-opt: bool opts. like -e -abc
                } elseif (!$isLong && $val === true) {
                    foreach (str_split($opt) as $char) {
                        $sOpts[$char] = true;
                    }

                    continue;
                }

                $val = self::filterBool($val);
                $isArray = isset($arrayValues[$opt]);

                if ($isLong) {
                    if ($isArray) {
                        $lOpts[$opt][] = $val;
                    } else {
                        $lOpts[$opt] = $val;
                    }
                } else {
                    if ($isArray) {
                        $sOpts[$opt][] = $val;
                    } else {
                        $sOpts[$opt] = $val;
                    }
                }

                // arguments: param doesn't belong to any option, define it is args
            } else {
                // value specified inline (<arg>=<value>)
                if (strpos($p, '=') !== false) {
                    list($name, $val) = explode('=', $p, 2);
                    $args[$name] = self::filterBool($val);
                } else {
                    $args[] = $p;
                }
            }
        }

        if ($config['mergeOpts']) {
            return [$args, array_merge($sOpts, $lOpts)];
        }

        return [$args, $sOpts, $lOpts];
    }

    /**
     * @param string|bool $val
     * @param bool $enable
     * @return bool|mixed
     */
    public static function filterBool($val, $enable = true)
    {
        if ($enable) {
            if (\is_bool($val) || is_numeric($val)) {
                return $val;
            }

            // check it is a bool value.
            if (false !== stripos(self::TRUE_WORDS, "|$val|")) {
                return true;
            }

            if (false !== stripos(self::FALSE_WORDS, "|$val|")) {
                return false;
            }
        }

        return $val;
    }

    /**
     * @param mixed $val
     * @return bool
     */
    public static function nextIsValue($val): bool
    {
        // current() fetch error, will return FALSE
        if ($val === false) {
            return false;
        }

        // if is: '', 0
        if (!$val) {
            return true;
        }

        // it isn't option or named argument
        return $val{0} !== '-' && false === strpos($val, '=');
    }


    /**
     * Returns true if STDOUT supports colorization.
     * This code has been copied and adapted from
     * \Symfony\Component\Console\Output\OutputStream.
     * @return boolean
     */
    public static function supportColor(): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return
                '10.0.10586' === PHP_WINDOWS_VERSION_MAJOR . '.' . PHP_WINDOWS_VERSION_MINOR . '.' . PHP_WINDOWS_VERSION_BUILD ||
                // 0 == strpos(PHP_WINDOWS_VERSION_MAJOR . '.' . PHP_WINDOWS_VERSION_MINOR . PHP_WINDOWS_VERSION_BUILD, '10.') ||
                false !== getenv('ANSICON') ||
                'ON' === getenv('ConEmuANSI') ||
                'xterm' === getenv('TERM')// || 'cygwin' === getenv('TERM')
                ;
        }

        if (!\defined('STDOUT')) {
            return false;
        }

        return self::isInteractive(STDOUT);
    }

    /**
     * @return bool
     */
    public static function isSupport256Color(): bool
    {
        return DIRECTORY_SEPARATOR === '/' && strpos(getenv('TERM'), '256color') !== false;
    }

    /**
     * Returns if the file descriptor is an interactive terminal or not.
     * @param  int|resource $fileDescriptor
     * @return boolean
     */
    public static function isInteractive($fileDescriptor): bool
    {
        return \function_exists('posix_isatty') && @posix_isatty($fileDescriptor);
    }

}
