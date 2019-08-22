<?php declare(strict_types=1);

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Http\Server\Exception\HttpServerException;

/**
 * Class RequestMiddleware
 *
 * @Bean()
 *
 * @since 2.0
 */
class RequestMiddleware implements MiddlewareInterface
{
    /**
     * Handle request
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws HttpServerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Fix Chrome ico request bug
        if ($request->getUri()->getPath() === '/favicon.ico') {
            throw new HttpServerException('access favicon.ico');
        }

        // Handle
        $response = $handler->handle($request);

        // Power by
        return $response->withAddedHeader('X-Powered-By', 'Swoft');
    }
}