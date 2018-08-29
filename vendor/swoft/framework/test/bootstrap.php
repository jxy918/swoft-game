<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';
require_once dirname(__FILE__, 2) . '/test/config/define.php';

// init
\Swoft\Bean\BeanFactory::init();

/* @var \Swoft\Bootstrap\Boots\Bootable $bootstrap */
$bootstrap = \Swoft\App::getBean(\Swoft\Bootstrap\Bootstrap::class);
$bootstrap->bootstrap();

\Swoft\Bean\BeanFactory::reload();

$initApplicationContext = new \Swoft\Core\InitApplicationContext();
$initApplicationContext->init();
\Swoft\App::$isInTest = true;
