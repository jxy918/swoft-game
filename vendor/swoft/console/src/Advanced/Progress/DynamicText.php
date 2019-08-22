<?php declare(strict_types=1);

namespace Swoft\Console\Advanced\Progress;

use Generator;
use Swoft\Console\Advanced\NotifyMessage;
use Swoft\Console\Console;
use Toolkit\Cli\Cli;
use function printf;

/**
 * Class DynamicText
 * @package Swoft\Console\Advanced\Progress
 */
class DynamicText extends NotifyMessage
{
    /**
     * @param string $doneMsg
     * @param string $fixedMsg
     * @return Generator
     */
    public static function gen(string $doneMsg, string $fixedMsg = ''): Generator
    {
        $counter  = 0;
        $finished = false;
        // $template = Cli::isSupportColor() ? "\x0D\x1B[2K" : "\x0D\r";
        $template = Cli::isSupportColor() ? "\x0D\x1B[2K" : "\x0D";

        if ($fixedMsg) {
            $template .= Console::style()->render($fixedMsg);
        }

        $template .= '%s';
        $doneMsg  = $doneMsg ? Console::style()->render($doneMsg) : '';

        while (true) {
            if ($finished) {
                break;
            }

            $msg = yield;

            if ($msg === false) {
                $msg = $doneMsg ?: '';
                $counter++;
                $finished = true;
            }

            printf($template, $msg);

            if ($finished) {
                echo "\n";
                break;
            }
        }

        yield $counter;
    }

    public function display(): void
    {

    }
}
