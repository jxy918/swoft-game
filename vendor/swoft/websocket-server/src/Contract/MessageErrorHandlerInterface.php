<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Contract;

use Swoft\Error\Contract\ErrorHandlerInterface;
use Swoole\WebSocket\Frame;
use Throwable;

/**
 * Interface MessageErrorHandlerInterface
 *
 * @since 2.0
 */
interface MessageErrorHandlerInterface extends ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     * @param Frame     $frame
     */
    public function handle(Throwable $e, Frame $frame): void;
}
