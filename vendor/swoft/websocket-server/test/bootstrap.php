<?php

if (file_exists($file = dirname(__DIR__, 3) . '/autoload.php')) {
    require $file;
} elseif (file_exists($file = dirname(__DIR__) . '/vendor/autoload.php')) {
    require $file;
} else {
    exit('OO, The composer autoload file is not found!');
}

require 'config/define.php';

// init
\Swoft\App::$isInTest = true;
\Swoft\Bean\BeanFactory::init();

/* @var \Swoft\Bootstrap\Boots\Bootable $bootstrap */
$bootstrap = \Swoft\App::getBean(\Swoft\Bootstrap\Bootstrap::class);
$bootstrap->bootstrap();

\Swoft\Bean\BeanFactory::reload([
    'application' => [
        'class'  => \Swoft\Testing\Application::class,
        'inTest' => true
    ],
]);
$initApplicationContext = new \Swoft\Core\InitApplicationContext();
$initApplicationContext->init();
