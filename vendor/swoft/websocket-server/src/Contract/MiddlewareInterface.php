<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Contract;

/**
 * Interface MiddlewareInterface
 *
 * @since 2.0
 */
interface MiddlewareInterface
{
    /**
     * @param RequestInterface        $request
     * @param MessageHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, MessageHandlerInterface $handler): ResponseInterface;
}
