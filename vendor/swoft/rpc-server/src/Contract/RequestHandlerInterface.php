<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Contract;

/**
 * Class RequestHandlerInterface
 *
 * @since 2.0
 */
interface RequestHandlerInterface
{
    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface;
}