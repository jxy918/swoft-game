<?php

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Http\Message\Bean\Collector\MiddlewareCollector;
use Swoft\Core\RequestHandler;
use Swoft\Bean\Annotation\Bean;
use Swoft\Http\Server\AttributeEnum;
use Swoft\Http\Message\Middleware\MiddlewareInterface;

/**
 * the annotation middlewares of action
 *
 * @Bean()
 */
class UserMiddleware implements MiddlewareInterface
{
    /**
     * do middlewares of action
     *
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $httpHandler = $request->getAttribute(AttributeEnum::ROUTER_ATTRIBUTE);
        $info = $httpHandler[2];

        $actionMiddlewares = [];
        if (isset($info['handler']) && \is_string($info['handler'])) {
            // Extract action info from router handler
            $exploded             = explode('@', $info['handler']);
            $controllerClass      = $exploded[0] ?? '';
            $action               = $exploded[1] ?? '';

            $collector = MiddlewareCollector::getCollector();
            $collectedMiddlewares = $collector[$controllerClass]['middlewares']??[];

            // Get group middleware from Collector
            if ($controllerClass) {
                $collect = $collectedMiddlewares['group'] ?? [];
                $collect && $actionMiddlewares = array_merge($actionMiddlewares, $collect);
            }
            // Get the specified action middleware from Collector
            if ($action) {
                $collect = $collectedMiddlewares['actions'][$action]??[];
                $collect && $actionMiddlewares = array_merge($actionMiddlewares, $collect ?? []);
            }
        }
        if (!empty($actionMiddlewares) && $handler instanceof RequestHandler) {
            $handler->insertMiddlewares($actionMiddlewares);
        }

        return $handler->handle($request);
    }
}
