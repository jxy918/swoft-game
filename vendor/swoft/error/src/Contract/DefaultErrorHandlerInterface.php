<?php declare(strict_types=1);

namespace Swoft\Error\Contract;

use Throwable;

/**
 * Interface DefaultErrorHandlerInterface
 *
 * @since 2.0
 */
interface DefaultErrorHandlerInterface extends ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     *
     * @return void
     */
    public function handle(Throwable $e): void;
}
