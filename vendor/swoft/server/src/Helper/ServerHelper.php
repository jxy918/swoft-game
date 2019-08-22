<?php declare(strict_types=1);

namespace Swoft\Server\Helper;

use Swoole\Process;
use function file_exists;
use function sleep;
use function time;
use function unlink;
use function usleep;

/**
 * Class ServerHelper
 *
 * @since 2.0
 */
class ServerHelper
{
    /**
     * Do shutdown process and wait it exit.
     *
     * @param int    $pid      Process Pid
     * @param int    $signal   SIGTERM = 15
     * @param string $name
     * @param int    $waitTime Seconds
     *
     * @return bool
     */
    public static function killAndWait(
        int $pid,
        int $signal = 15,
        string $name = 'process',
        int $waitTime = 10
    ): bool {
        // Do stop
        if (!self::sendSignal($pid, $signal)) {
            echo "Send stop signal to the $name(PID:$pid) failed!" . PHP_EOL;
            return false;
        }

        // not wait, only send signal
        if ($waitTime <= 0) {
            echo "The $name process stopped." . PHP_EOL;
            return true;
        }

        $errorMsg  = '';
        $startTime = time();
        echo 'Stopping .';

        // wait exit
        while (true) {
            if (!self::isRunning($pid)) {
                break;
            }

            if (time() - $startTime > $waitTime) {
                $errorMsg = "Stop the $name(PID:$pid) failed(timeout:{$waitTime}s)!";
                break;
            }

            echo '.';
            sleep(1);
        }

        if ($errorMsg) {
            echo PHP_EOL . $errorMsg . PHP_EOL;
            return false;
        }

        echo ' Successful!' . PHP_EOL;
        return true;
    }

    /**
     * Send signal to the server process
     *
     * @param int $pid
     * @param int $signal
     * @param int $timeout
     *
     * @return bool
     */
    public static function sendSignal(int $pid, int $signal, int $timeout = 0): bool
    {
        if ($pid <= 0) {
            return false;
        }

        // do send
        if ($ret = Process::kill($pid, $signal)) {
            return true;
        }

        // don't want retry
        if ($timeout <= 0) {
            return $ret;
        }

        // failed, try again ...
        $timeout   = $timeout > 0 && $timeout < 10 ? $timeout : 3;
        $startTime = time();

        // retry stop if not stopped.
        while (true) {
            // success
            if (!$isRunning = Process::kill($pid, 0)) {
                break;
            }

            // have been timeout
            if ((time() - $startTime) >= $timeout) {
                return false;
            }

            // try again kill
            $ret = Process::kill($pid, $signal);
            usleep(10000);
        }

        return $ret;
    }

    /**
     * @param string $pidFile
     *
     * @return bool
     */
    public static function removePidFile(string $pidFile): bool
    {
        if ($pidFile && file_exists($pidFile)) {
            return unlink($pidFile);
        }

        return true;
    }

    /**
     * @param int $pid
     *
     * @return bool
     */
    public static function isRunning(int $pid): bool
    {
        return ($pid > 0) && Process::kill($pid, 0);
    }

    /**
     * Check swoole is > 4.4.0
     *
     * @return bool
     */
    public static function isGtSwoole44(): bool
    {
        return SWOOLE_VERSION_ID > 40400;
    }

    /**
     * Check swoole is >= 4.4.0
     *
     * @return bool
     */
    public static function isGteSwoole44(): bool
    {
        return SWOOLE_VERSION_ID >= 40400;
    }
}
