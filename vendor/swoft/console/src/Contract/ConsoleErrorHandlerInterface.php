<?php declare(strict_types=1);

namespace Swoft\Console\Contract;

use Swoft\Error\Contract\ErrorHandlerInterface;
use Throwable;

/**
 * Interface ConsoleErrorHandlerInterface
 *
 * @since 2.0.3
 */
interface ConsoleErrorHandlerInterface extends ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     */
    public function handle(Throwable $e): void;
}
