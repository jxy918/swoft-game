<?php

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Collector\ValidatorCollector;
use Swoft\Http\Server\AttributeEnum;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\Http\Server\Validator\HttpValidator;

/**
 * Validator middleware
 * @Bean()
 */
class ValidatorMiddleware implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Swoft\Exception\ValidatorException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $httpHandler = $request->getAttribute(AttributeEnum::ROUTER_ATTRIBUTE);
        $info = $httpHandler[2];

        if (isset($info['handler']) && \is_string($info['handler'])) {
            $exploded = explode('@', $info['handler']);
            $className = $exploded[0] ?? '';
            $validatorKey = $exploded[1] ?? '';
            $matches = $info['matches'] ?? [];

            /* @var HttpValidator $validator */
            $validator = App::getBean(HttpValidator::class);
            $collector = ValidatorCollector::getCollector();

            if (isset($collector[$className][$validatorKey]['validator'])) {
                $validators = $collector[$className][$validatorKey]['validator'];
                $request = $validator->validate($validators, $request, $matches);
            }
        }

        return $handler->handle($request);
    }
}
