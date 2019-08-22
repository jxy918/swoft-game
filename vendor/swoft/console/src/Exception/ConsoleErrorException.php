<?php declare(strict_types=1);

namespace Swoft\Console\Exception;

use Throwable;

/**
 * Class ConsoleErrorException
 *
 * @since 2.0.3
 */
class ConsoleErrorException extends ConsoleException
{
    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     *
     * @throws ConsoleErrorException
     */
    public static function throw(string $message, int $code = 0, Throwable $previous = null): void
    {
        throw new self($message, $code, $previous);
    }
}
