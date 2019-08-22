<?php declare(strict_types=1);

namespace Swoft\Log;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Co;
use Swoft\Log\Helper\CLog;
use Swoft\Log\Helper\Log;

/**
 * Class Debug
 *
 * @since 2.0
 */
class Debug
{
    /**
     * @param string $message
     * @param mixed  ...$params
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function log(string $message, ...$params)
    {
        // Console log
        CLog::debug($message, ...$params);

        // In coroutine to write application log
        if (Co::id() > 0) {
            Log::debug($message, ...$params);
        }
    }
}
