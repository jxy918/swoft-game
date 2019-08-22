<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Contract;

use Swoft\Error\Contract\ErrorHandlerInterface;
use Throwable;

/**
 * Class TcpCloseErrorHandlerInterface
 *
 * @since 2.0.3
 */
interface TcpCloseErrorHandlerInterface extends ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     * @param int       $fd
     */
    public function handle(Throwable $e, int $fd): void;
}
