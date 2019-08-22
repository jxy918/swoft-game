<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Contract;

use Swoft\Error\Contract\ErrorHandlerInterface;
use Throwable;

/**
 * Interface CloseErrorHandlerInterface
 *
 * @since 2.0
 */
interface CloseErrorHandlerInterface extends ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     * @param int       $fd
     */
    public function handle(Throwable $e, int $fd): void;
}
