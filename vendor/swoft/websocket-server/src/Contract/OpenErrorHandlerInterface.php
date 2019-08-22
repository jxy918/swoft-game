<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Contract;

use Swoft\Error\Contract\ErrorHandlerInterface;
use Swoft\Http\Message\Request;
use Throwable;

/**
 * Interface OpenErrorHandlerInterface
 *
 * @since 2.0
 */
interface OpenErrorHandlerInterface extends ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     * @param Request   $request
     *
     * @return void
     */
    public function handle(Throwable $e, Request $request): void;
}
