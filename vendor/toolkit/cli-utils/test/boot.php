<?php
/**
 * phpunit
 */

error_reporting(E_ALL);
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(function ($class) {
    $file = null;

    if (0 === strpos($class, 'Toolkit\Cli\Example\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\Cli\Example\\')));
        $file = dirname(__DIR__) . "/example/{$path}.php";
    } elseif (0 === strpos($class, 'Toolkit\CliTest\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\CliTest\\')));
        $file = __DIR__ . "/{$path}.php";
    } elseif (0 === strpos($class, 'Toolkit\Cli\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Toolkit\Cli\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});
