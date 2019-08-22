<?php declare(strict_types=1);

namespace SwoftTest\WebSocket\Server\Testing;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\WebSocket\Server\Contract\MessageHandlerInterface;
use Swoft\WebSocket\Server\Contract\MiddlewareInterface;
use Swoft\WebSocket\Server\Contract\RequestInterface;
use Swoft\WebSocket\Server\Contract\ResponseInterface;

/**
 * Class User2Middleware
 *
 * @Bean()
 */
class User2Middleware implements MiddlewareInterface
{
    /**
     * @param RequestInterface        $request
     * @param MessageHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, MessageHandlerInterface $handler): ResponseInterface
    {
        $start = '>user2 ';

        $resp = $handler->handle($request);

        return $resp->setData($start . $resp->getData() . ' user2>');
    }
}
