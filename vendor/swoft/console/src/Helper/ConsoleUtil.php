<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2018/3/14
 * Time: 下午3:21
 */

namespace Swoft\Console\Helper;

use Swoft\Console\Style\Style;

/**
 * Class ConsoleUtil
 * @package Swoft\Console\Helper
 */
class ConsoleUtil
{
    const LOG_LEVEL2TAG = [
        'info' => 'info',
        'warn' => 'warning',
        'warning' => 'warning',
        'debug' => 'cyan',
        'notice' => 'notice',
        'error' => 'error',
    ];

    /**
     * print log to console
     * @param string $msg
     * @param array $data
     * @param string $type
     * @param array $opts
     * [
     *  '_category' => 'application',
     *  'process' => 'work',
     *  'pid' => 234,
     *  'coId' => 12,
     * ]
     */
    public static function log(string $msg, array $data = [], string $type = 'info', array $opts = [])
    {
        if (isset(self::LOG_LEVEL2TAG[$type])) {
            $type = Style::wrap(\strtoupper($type), self::LOG_LEVEL2TAG[$type]);
        }

        $userOpts = [];

        foreach ($opts as $n => $v) {
            if (\is_numeric($n) || $n[0] === '_') {
                $userOpts[] = "[$v]";
            } else {
                $userOpts[] = "[$n:$v]";
            }
        }

        $optString = $userOpts ? ' ' . \implode(' ', $userOpts) : '';

        \output()->writeln(\sprintf(
            '%s [%s]%s %s %s',
            \date('Y/m/d H:i:s'),
            $type,
            $optString,
            \trim($msg),
            $data ? PHP_EOL . \json_encode($data, \JSON_UNESCAPED_SLASHES|\JSON_PRETTY_PRINT) : ''
        ));
    }

    /**
     * 与文本进度条相比，没有 total - 不会显示进度百分比
     *
     * ```php
     *  $total = 120;
     *  $ctt = ConsoleUtil::counterTxt('doing ...', 'completed.');
     *  $this->write('Counter:');
     *  while ($total - 1) {
     *      $ctt->send(1);
     *      usleep(30000);
     *      $total--;
     *  }
     *  // end of the counter.
     *  $ctt->send(-1);
     * ```
     * @param string $msg
     * @param string|null $doneMsg
     * @return \Generator
     */
    public static function counterTxt(string $msg, $doneMsg = null)
    {
        $counter = 0;
        $finished = false;
        $tpl = (CommandHelper::supportColor() ? "\x0D\x1B[2K" : "\x0D\r") . '%d %s';
        $msg = style()->t($msg);
        $doneMsg = $doneMsg ? style()->t($doneMsg) : null;
        while (true) {
            if ($finished) {
                return;
            }

            $step = yield;

            if ((int)$step <= 0) {
                $counter++;
                $finished = true;
                $msg = $doneMsg ?: $msg;
            } else {
                $counter += $step;
            }

            printf($tpl, $counter, $msg);

            if ($finished) {
                echo "\n";
                break;
            }
        }

        yield false;
    }

    /**
     * read CLI input
     * @param mixed $message
     * @param bool $nl
     * @param array $opts
     * [
     *   'stream' => \STDIN
     * ]
     * @return string
     */
    public static function read($message = null, $nl = false, array $opts = []): string
    {
        if ($message) {
            \output()->writeln($message, $nl);
        }

        $stream = $opts['stream'] ?? \STDIN;

        return trim(fgets($stream));
    }

    /**
     * 确认, 发出信息要求确认
     * @param string $question 发出的信息
     * @param bool $default Default value
     * @return bool
     */
    public static function confirm(string $question, $default = true): bool
    {
        if (!$question = trim($question)) {
            \output()->writeln('Please provide a question message!', true, 2);
        }

        $question = ucfirst(trim($question, '?'));
        $default = (bool)$default;
        $defaultText = $default ? 'yes' : 'no';
        $message = "<comment>$question ?</comment>\nPlease confirm (yes|no)[default:<info>$defaultText</info>]: ";

        while (true) {
            \output()->writeln($message, false);
            $answer = \input()->read();

            if (empty($answer)) {
                return $default;
            }

            if (0 === stripos($answer, 'y')) {
                return true;
            }

            if (0 === stripos($answer, 'n')) {
                return false;
            }
        }

        return false;
    }
}
