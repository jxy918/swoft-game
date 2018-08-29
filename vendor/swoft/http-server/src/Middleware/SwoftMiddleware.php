<?php

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Http\Server\Exception\NotAcceptableException;
use Swoft\Http\Message\Middleware\MiddlewareInterface;

/**
 * @Bean()
 * Merge all swoft middleware to this one middleware for performance
 */
class SwoftMiddleware implements MiddlewareInterface
{
    use AcceptTrait, RouterTrait;

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Swoft\Http\Server\Exception\NotAcceptableException
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Fix Chrome ico request bug
        if ($request->getUri()->getPath() === '/favicon.ico') {
            throw new NotAcceptableException('access favicon.ico');
        }

        // Parser
        /* @var \Swoft\Http\Server\Parser\RequestParserInterface $requestParser */
        $requestParser = App::getBean('requestParser');
        $request = $requestParser->parse($request);

        // Router
        $request = $this->handleRouter($request);

        // Delegate to next handler
        $response = $handler->handle($request);

        // Power by
        $response = $response->withAddedHeader('X-Powered-By', 'Swoft');

        // Response handler, according to Accept
        $response = $this->handleAccept($request, $response);

        return $response;
    }
}
