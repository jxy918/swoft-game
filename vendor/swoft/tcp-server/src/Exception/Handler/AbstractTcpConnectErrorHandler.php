<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Exception\Handler;

use Swoft\Error\ErrorType;
use Swoft\Tcp\Server\Contract\TcpConnectErrorHandlerInterface;

/**
 * Class AbstractTcpServerErrorHandler
 *
 * @since 2.0
 */
abstract class AbstractTcpConnectErrorHandler implements TcpConnectErrorHandlerInterface
{
    /**
     * @return int
     */
    public function getType(): int
    {
        return ErrorType::TCP_CNT;
    }
}
