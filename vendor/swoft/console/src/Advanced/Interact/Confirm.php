<?php declare(strict_types=1);

namespace Swoft\Console\Advanced\Interact;

use Swoft\Console\Advanced\InteractMessage;
use Swoft\Console\Console;
use Swoft\Console\Helper\Show;
use Swoft\Console\Input\Input;
use function sprintf;
use function stripos;
use function trim;
use function ucfirst;

/**
 * Class Confirm
 * @package Swoft\Console\Advanced\Interact
 */
class Confirm extends InteractMessage
{
    /**
     * Send a message request confirmation
     *
     * @param string $question The question message
     * @param bool   $default  Default value
     * @param bool   $nl
     *
     * @return bool
     */
    public static function ask(string $question, bool $default = true, bool $nl = true): bool
    {
        if (!$question = trim($question)) {
            Show::warning('Please provide a question message!', 1);
            return false;
        }

        $defText  = $default ? 'yes' : 'no';
        $question = ucfirst(trim($question, '?'));
        $template = '<comment>%s ?</comment>%sPlease confirm(yes|no)[default:<info>%s</info>]: ';
        $message  = sprintf($template, $question, $nl ? "\n" : '', $defText);

        while (true) {
            $answer = Console::readChar($message);
            if ('' === $answer) {
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

    /**
     * @param string $question
     * @param bool   $default
     * @param bool   $nl
     *
     * @return bool
     */
    public static function yes(string $question, bool $default = true, bool $nl = true): bool
    {
        return self::ask($question, $default, $nl);
    }

    /**
     * @param string $question
     * @param bool   $default
     * @param bool   $nl
     *
     * @return bool
     */
    public static function not(string $question, bool $default = true, bool $nl = true): bool
    {
        return self::ask($question, $default, $nl) === false;
    }
}
