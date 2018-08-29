<?php

namespace Swoft\Helper;

/**
 * Class EnvHelper
 *
 * @package Swoft\Helper
 */
class EnvHelper
{
    /**
     * @throws \RuntimeException
     */
    public static function check()
    {
        if (!PhpHelper::isCli()) {
            throw new \RuntimeException('Server must run in the CLI mode.');
        }

        if (!version_compare(PHP_VERSION, '7.0')) {
            throw new \RuntimeException('Run the server requires PHP version >= 7.0');
        }

        if (!\extension_loaded('swoole')) {
            throw new \RuntimeException("Run the server, extension 'swoole 2.x' is required!");
        }

        if (!class_exists('Swoole\Coroutine')) {
            throw new \RuntimeException("The swoole is must enable coroutine by build param '--enable-coroutine'!");
        }

        if (\extension_loaded('blackfire')) {
            throw new \RuntimeException('The extension of blackfire must be closed, otherwise swoft will be affected!');
        }

        if (\extension_loaded('xdebug')) {
            throw new \RuntimeException('The extension of xdebug must be closed, otherwise swoft will be affected!');
        }

        if (\extension_loaded('uopz') && !ini_get('uopz.disable')) {
            throw new \RuntimeException('The extension of uopz must be closed, otherwise swoft will be affected!');
        }

        if (\extension_loaded('xhprof')) {
            throw new \RuntimeException('The extension of xhprof must be closed, otherwise swoft will be affected!');
        }

        if (\extension_loaded('zend')) {
            throw new \RuntimeException('The extension of zend must be closed, otherwise swoft will be affected!');
        }
    }
}