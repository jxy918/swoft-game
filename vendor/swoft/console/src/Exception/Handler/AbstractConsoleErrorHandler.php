<?php declare(strict_types=1);

namespace Swoft\Console\Exception\Handler;

use Swoft\Console\Contract\ConsoleErrorHandlerInterface;
use Swoft\Error\ErrorType;

/**
 * Class AbstractConsoleErrorHandler
 *
 * @since 2.0.3
 */
abstract class AbstractConsoleErrorHandler implements ConsoleErrorHandlerInterface
{
    /**
     * @return int
     */
    public function getType(): int
    {
        return ErrorType::CLI;
    }
}
