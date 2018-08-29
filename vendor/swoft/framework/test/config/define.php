<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
use \Swoft\App;

! defined('DS') && define('DS', DIRECTORY_SEPARATOR);
! defined('APP_NAME') && define('APP_NAME', 'swoft');
! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
! defined('COMMAND_NS') && define('COMMAND_NS', "App\Commands");

$aliases = [
    '@root' => BASE_PATH,
    '@app' => '@root/app',
    '@res' => '@root/resources',
    '@runtime' => '@root/runtime',
    '@configs' => '@root/config',
    '@resources' => '@root/resources',
    '@beans' => '@configs/beans',
    '@properties' => '@configs/properties',
    '@console' => '@beans/console.php',
];
App::setAliases($aliases);
