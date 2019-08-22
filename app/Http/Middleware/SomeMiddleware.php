<?php
/**
 * Created by PhpStorm.
 * User: jiang
 * Date: 2019/7/12
 * Time: 16:25
 */

namespace App\Http\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Context\Context;
use Swoft\Http\Server\Contract\MiddlewareInterface;
/**
 * @Bean()
 */
class SomeMiddleware implements MiddlewareInterface
{

    /**
     * Process an incoming server request.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if ($path === '/favicon.ico') {
            $response = Context::mustGet()->getResponse();
            return $response->withStatus(404);
        }
        return $handler->handle($request);
    }
}