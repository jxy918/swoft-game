<?php

namespace Swoft\Rpc\Server\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Collector\ValidatorCollector;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\Rpc\Server\Validator\ServiceValidator;
use Swoft\Validator\ValidatorInterface;

/**
 * the middleware of service middleware
 *
 * @Bean()
 * @uses      ValidatorMiddleware
 * @version   2017年12月10日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ValidatorMiddleware implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var ValidatorInterface $validator */
        $serviceHandler = $request->getAttribute(RouterMiddleware::ATTRIBUTE);
        $serviceData    = $request->getAttribute(PackerMiddleware::ATTRIBUTE_DATA);
        $validator      = App::getBean(ServiceValidator::class);

        list($className, $validatorKey) = $serviceHandler;
        $collector = ValidatorCollector::getCollector();
        if (isset($collector[$className][$validatorKey]['validator'])) {
            $validators = $collector[$className][$validatorKey]['validator'];
            $validator->validate($validators, $serviceHandler, $serviceData);
        }

        return $handler->handle($request);
    }
}
