<?php

namespace Swoft\Rpc\Server\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Http\Message\Middleware\MiddlewareInterface;

/**
 * service router
 *
 * @Bean()
 * @uses      RouterMiddleware
 * @version   2017年11月26日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RouterMiddleware implements MiddlewareInterface
{
    /**
     * the attributed key of service
     */
    const ATTRIBUTE = "serviceHandler";

    /**
     * get handler from router
     *
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // service data
        $data = $request->getAttribute(PackerMiddleware::ATTRIBUTE_DATA);

        $method    = $data['method']??"";
        $version   = $data['version']??"";
        $interface = $data['interface']??"";

        /* @var \Swoft\Rpc\Server\Router\HandlerMapping $serviceRouter */
        $serviceRouter  = App::getBean('serviceRouter');
        $serviceHandler = $serviceRouter->getHandler($interface, $version, $method);

        // deliver service data
        $request = $request->withAttribute(self::ATTRIBUTE, $serviceHandler);

        return $handler->handle($request);
    }
}
