<?php

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Bean\Annotation\Bean;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\Http\Server\Exception\NotAcceptableException;

/**
 * fix chrome bug
 *
 * @Bean()
 * @uses      FaviconIcoMiddleware
 * @version   2017年11月16日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class FaviconIcoMiddleware implements MiddlewareInterface
{

    /**
     * fix the bug of chrome
     *
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Swoft\Http\Server\Exception\NotAcceptableException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Fix Chrome ico request bug
        if ($request->getUri()->getPath() === '/favicon.ico') {
            throw new NotAcceptableException('access favicon.ico');
        }

        return $handler->handle($request);
    }
}
