<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Exception;

use Swoft\Server\Exception\ServerException;

/**
 * Class WsServerException
 *
 * @since 2.0
 */
class WsServerException extends ServerException
{
    /**
     * @param string $message
     * @param int    $code
     *
     * @return WsServerException
     */
    public static function make(string $message, int $code = 500): self
    {
        return new static($message, $code);
    }
}
