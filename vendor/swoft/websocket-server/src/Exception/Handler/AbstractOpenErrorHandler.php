<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Exception\Handler;

use Swoft\Error\ErrorType;
use Swoft\WebSocket\Server\Contract\OpenErrorHandlerInterface;

/**
 * Class AbstractCloseErrorHandler
 *
 * @since 2.0
 */
abstract class AbstractOpenErrorHandler implements OpenErrorHandlerInterface
{
    /**
     * @return int
     */
    public function getType(): int
    {
        return ErrorType::WS_OPN;
    }
}
