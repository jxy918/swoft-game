<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Contract;

/**
 * Class MiddlewareInterface
 *
 * @since 2.0
 */
interface MiddlewareInterface
{
    /**
     * @param RequestInterface        $request
     * @param RequestHandlerInterface $requestHandler
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $requestHandler): ResponseInterface;
}