<?php

namespace Swoft\Rpc\Server\Router;

use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\PhpHelper;
use Swoft\Helper\ResponseHelper;
use Swoft\Http\Message\Router\HandlerAdapterInterface;
use Swoft\Rpc\Server\Middleware\PackerMiddleware;
use Swoft\Rpc\Server\Rpc\Response;

/**
 * Service handler adapter
 * @Bean("serviceHandlerAdapter")
 */
class HandlerAdapter implements HandlerAdapterInterface
{
    /**
     * The result of service handler
     */
    const ATTRIBUTE = 'serviceResult';

    /**
     * Execute service handler
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array                                    $handler
     * @return Response
     */
    public function doHandler(ServerRequestInterface $request, array $handler): Response
    {
        // the function params of service
        $data = $request->getAttribute(PackerMiddleware::ATTRIBUTE_DATA);
        $params = $data['params'] ?? [];

        list($serviceClass, $method) = $handler;
        $service = App::getBean($serviceClass);

        // execute handler with params
        $response = PhpHelper::call([$service, $method], $params);
        $response = ResponseHelper::formatData($response);

        // response
        if (! $response instanceof Response) {
            $response = (new Response())->withAttribute(self::ATTRIBUTE, $response);
        }

        return $response;
    }
}
