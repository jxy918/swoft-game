<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Exception\Handler;

use Swoft\Error\ErrorType;
use Swoft\WebSocket\Server\Contract\MessageErrorHandlerInterface;

/**
 * Class AbstractMessageErrorHandler
 *
 * @since 2.0
 */
abstract class AbstractMessageErrorHandler implements MessageErrorHandlerInterface
{
    /**
     * @return int
     */
    public function getType(): int
    {
        return ErrorType::WS_MSG;
    }
}
