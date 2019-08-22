<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Exception\Handler;


use Swoft\Error\ErrorType;
use Swoft\Rpc\Server\Contract\RpcServerErrorHandlerInterface;

/**
 * Class AbstractRpcServerErrorHandler
 *
 * @since 2.0
 */
abstract class AbstractRpcServerErrorHandler implements RpcServerErrorHandlerInterface
{
    /**
     * @return int
     */
    public function getType(): int
    {
        return ErrorType::RPC;
    }
}