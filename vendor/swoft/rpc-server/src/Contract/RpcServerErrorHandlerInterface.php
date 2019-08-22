<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Contract;


use Swoft\Error\Contract\ErrorHandlerInterface;
use Swoft\Rpc\Server\Response;
use Throwable;

/**
 * Class RpcServerErrorHandlerInterface
 *
 * @since 2.0
 */
interface RpcServerErrorHandlerInterface extends ErrorHandlerInterface
{
    /**
     * @param Throwable $e
     * @param Response  $response
     *
     * @return Response
     */
    public function handle(Throwable $e, Response $response): Response;
}