<?php declare(strict_types=1);


namespace Swoft\Log\Helper;

use Monolog\Formatter\LineFormatter;
use function sprintf;
use Swoft\Log\CLogger;
use Swoft\Log\Handler\CEchoHandler;
use Swoft\Log\Handler\CFileHandler;

/**
 * Class CLog
 *
 * @since 2.0
 */
class CLog
{
    /**
     * @var CLogger
     */
    private static $cLogger;

    /**
     * Init console logger
     *
     * @param array $config
     */
    public static function init(array $config): void
    {
        if (self::$cLogger !== null) {
            return;
        }

        $name    = $config['name'] ?? '';
        $enable  = $config['enable'] ?? true;
        $output  = $config['output'] ?? true;
        $levels  = $config['levels'] ?? '';
        $logFile = $config['logFile'] ?? '';

        $lineFormatter = new LineFormatter();

        $cEchoHandler = new CEchoHandler();
        $cEchoHandler->setFormatter($lineFormatter);
        $cEchoHandler->setLevels($levels);
        $cEchoHandler->setOutput($output);

        $cFileHandler = new CFileHandler();
        $cFileHandler->setFormatter($lineFormatter);
        $cFileHandler->setLevels($levels);
        $cFileHandler->setLogFile($logFile);

        $cLogger = new CLogger();
        $cLogger->setName($name);
        $cLogger->setEnable($enable);
        $cLogger->setHandlers([$cEchoHandler, $cFileHandler]);

        self::$cLogger = $cLogger;
    }

    /**
     * Debug message
     *
     * @param string $message
     * @param array  $params
     */
    public static function debug(string $message, ...$params): void
    {
        if ($params) {
            $message = sprintf($message, ...$params);
        }

        if (SWOFT_DEBUG) {
            self::$cLogger->debug($message, []);
        }
    }

    /**
     * Info message
     *
     * @param string $message
     * @param array  $params
     */
    public static function info(string $message, ...$params): void
    {
        if ($params) {
            $message = sprintf($message, ...$params);
        }

        self::$cLogger->info($message, []);
    }

    /**
     * Warning message
     *
     * @param string $message
     * @param array  $params
     */
    public static function warning(string $message, ...$params): void
    {
        if ($params) {
            $message = sprintf($message, ...$params);
        }

        self::$cLogger->warning($message, []);
    }

    /**
     * Error message
     *
     * @param string $message
     * @param array  $params
     */
    public static function error(string $message, ...$params): void
    {
        if ($params) {
            $message = sprintf($message, ...$params);
        }

        self::$cLogger->error($message, []);
    }
}
