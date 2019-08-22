<?php declare(strict_types=1);

namespace Swoft\Http\Server\Exception\Handler;

use Swoft\Error\ErrorType;
use Swoft\Http\Server\Contract\HttpErrorHandlerInterface;

/**
 * Class AbstractHttpErrorHandler
 *
 * @since 2.0
 */
abstract class AbstractHttpErrorHandler implements HttpErrorHandlerInterface
{
    /**
     * @return int
     */
    public function getType(): int
    {
        return ErrorType::HTTP;
    }
}
