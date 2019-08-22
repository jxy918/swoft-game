<?php declare(strict_types=1);

namespace Swoft\Stdlib\Helper;

use RuntimeException;
use function chdir;
use function exec;
use function fclose;
use function function_exists;
use function getcwd;
use function getmyuid;
use function implode;
use function is_resource;
use function ob_start;
use function passthru;
use function preg_match;
use function preg_replace;
use function proc_close;
use function proc_open;
use function shell_exec;
use function stream_get_contents;
use function sys_get_temp_dir;
use function system;
use function trim;

/**
 * Class SystemHelper
 *
 * @since 2.0
 */
class SystemHelper extends EnvHelper
{
    /**
     * Set cli process title
     *
     * @param string $title
     *
     * @return bool
     */
    public static function setProcessTitle(string $title): bool
    {
        if (EnvHelper::isMac()) {
            return false;
        }

        if (function_exists('cli_set_process_title')) {
            return @cli_set_process_title($title);
        }

        return true;
    }

    /**
     * Run a command. It is support windows
     *
     * @param string      $command
     * @param string|null $cwd
     *
     * @return array [$code, $output, $error]
     * @throws RuntimeException
     */
    public static function run(string $command, string $cwd = null): array
    {
        $descriptors = [
            0 => ['pipe', 'r'], // stdin - read channel
            1 => ['pipe', 'w'], // stdout - write channel
            2 => ['pipe', 'w'], // stdout - error channel
            3 => ['pipe', 'r'], // stdin - This is the pipe we can feed the password into
        ];

        $pipes   = [];
        $process = proc_open($command, $descriptors, $pipes, $cwd);

        if (!is_resource($process)) {
            throw new RuntimeException("Can't open resource with proc_open.");
        }

        // Nothing to push to input.
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // TODO: Write passphrase in pipes[3].
        fclose($pipes[3]);

        // Close all pipes before proc_close! $code === 0 is success.
        $code = proc_close($process);

        return [$code, $output, $error];
    }

    /**
     * Method to execute a command in the sys
     * Uses :
     * 1. system
     * 2. passthru
     * 3. exec
     * 4. shell_exec
     *
     * @param string      $command
     * @param bool        $returnStatus
     * @param string|null $cwd
     *
     * @return array|string
     */
    public static function execute(string $command, bool $returnStatus = true, string $cwd = null)
    {
        $exitStatus = 1;

        if ($cwd) {
            chdir($cwd);
        }

        // system
        if (function_exists('system')) {
            ob_start();
            system($command, $exitStatus);
            $output = ob_get_clean();

            // passthru
        } elseif (function_exists('passthru')) {
            ob_start();
            passthru($command, $exitStatus);
            $output = ob_get_clean();

            //exec
        } elseif (function_exists('exec')) {
            exec($command, $output, $exitStatus);
            $output = implode("\n", $output);

            //shell_exec
        } elseif (function_exists('shell_exec')) {
            $output = shell_exec($command);
        } else {
            $output     = 'Command execution not possible on this system';
            $exitStatus = 0;
        }

        if ($returnStatus) {
            return [
                'output' => trim($output),
                'status' => $exitStatus
            ];
        }

        return trim($output);
    }

    /**
     * Get unix user of current process.
     *
     * @return array
     */
    public static function getCurrentUser(): array
    {
        if (function_exists('posix_getpwuid')) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            return posix_getpwuid(getmyuid());
        }

        return [];
    }

    /**
     * @return string
     */
    public static function tempDir(): string
    {
        return self::getTempDir();
    }

    /**
     * @return string
     */
    public static function getTempDir(): string
    {
        // @codeCoverageIgnoreStart
        if (function_exists('sys_get_temp_dir')) {
            $tmp = sys_get_temp_dir();
        } elseif (!empty($_SERVER['TMP'])) {
            $tmp = $_SERVER['TMP'];
        } elseif (!empty($_SERVER['TEMP'])) {
            $tmp = $_SERVER['TEMP'];
        } elseif (!empty($_SERVER['TMPDIR'])) {
            $tmp = $_SERVER['TMPDIR'];
        } else {
            $tmp = getcwd();
        }
        // @codeCoverageIgnoreEnd

        return $tmp;
    }

    /**
     * get bash is available
     *
     * @return bool
     */
    public static function shIsAvailable(): bool
    {
        // $checkCmd = "/usr/bin/env bash -c 'echo OK'";
        // $shell = 'echo $0';
        $checkCmd = "sh -c 'echo OK'";

        return self::execute($checkCmd, false) === 'OK';
    }

    /**
     * get bash is available
     *
     * @return bool
     */
    public static function bashIsAvailable(): bool
    {
        // $checkCmd = "/usr/bin/env bash -c 'echo OK'";
        // $shell = 'echo $0';
        $checkCmd = "bash -c 'echo OK'";

        return self::execute($checkCmd, false) === 'OK';
    }

    /**
     * get screen size of the terminal
     *
     * ```php
     * list($width, $height) = Sys::getScreenSize();
     * ```
     *
     * @from Yii2
     *
     * @param boolean $refresh whether to force checking and not re-use cached size value.
     *                         This is useful to detect changing window size while the application is running but may
     *                         not get up to date values on every terminal.
     *
     * @return array|boolean An array of ($width, $height) or false when it was not able to determine size.
     */
    public static function getScreenSize(bool $refresh = false)
    {
        static $size;
        if ($size !== null && !$refresh) {
            return $size;
        }

        if (self::shIsAvailable()) {
            // try stty if available
            $stty = [];

            if (exec('stty -a 2>&1', $stty)
                && preg_match('/rows\s+(\d+);\s*columns\s+(\d+);/mi', implode(' ', $stty), $matches)
            ) {
                return ($size = [$matches[2], $matches[1]]);
            }

            // fallback to tput, which may not be updated on terminal resize
            if (($width = (int)exec('tput cols 2>&1')) > 0 && ($height = (int)exec('tput lines 2>&1')) > 0) {
                return ($size = [$width, $height]);
            }

            // fallback to ENV variables, which may not be updated on terminal resize
            if (($width = (int)getenv('COLUMNS')) > 0 && ($height = (int)getenv('LINES')) > 0) {
                return ($size = [$width, $height]);
            }
        }

        if (self::isWindows()) {
            $output = [];
            exec('mode con', $output);

            if (isset($output[1]) && strpos($output[1], 'CON') !== false) {
                return ($size = [
                    (int)preg_replace('~\D~', '', $output[3]),
                    (int)preg_replace('~\D~', '', $output[4])
                ]);
            }
        }

        return ($size = false);
    }
}
