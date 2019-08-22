<?php declare(strict_types=1);

namespace Swoft\Devtool;

use Swoft\App;
use Swoft\Console\Helper\ConsoleUtil;
use Swoft\Core\Coroutine;

/**
 * Class DevTool
 * @package Swoft\Devtool
 */
final class DevTool
{
    const VERSION = '1.0.0';
    const ROUTE_PREFIX = '/__devtool';

    public static $table;

    /**
     * @param string $msg
     * @param array $data
     * @param string $type
     * @param array $opts
     */
    public static function log(string $msg, array $data = [], string $type = 'debug', array $opts = [])
    {
        if (App::$server && !App::$server->isDaemonize()) {
            ConsoleUtil::log($msg, $data, $type, [
                'DevTool',
                'WorkerId' => App::getWorkerId(),
                'CoId' => Coroutine::tid()
            ]);
        }

        // if ($logger = App::getLogger()) {
        //     $logger->log($type, $msg, $data);
        // }
    }
}
