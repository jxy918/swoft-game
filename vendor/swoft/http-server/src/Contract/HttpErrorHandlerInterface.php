<?php

namespace Swoft\Http\Server\Contract;

use Swoft\Error\Contract\ErrorHandlerInterface;
use Swoft\Http\Message\Response;
use Throwable;


/**
 * Class HttpErrorHandlerInterface
 *
 * @since 2.0
 */
interface HttpErrorHandlerInterface extends ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     * @param Response   $response
     *
     * @return Response
     */
    public function handle(Throwable $e, Response $response): Response;
}
