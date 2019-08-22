<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Testing\Middleware;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Rpc\Server\Contract\MiddlewareInterface;
use Swoft\Rpc\Server\Contract\RequestHandlerInterface;
use Swoft\Rpc\Server\Contract\RequestInterface;
use Swoft\Rpc\Server\Contract\ResponseInterface;

/**
 * Class MethodMd
 *
 * @since 2.0
 *
 * @Bean()
 */
class MethodMd implements MiddlewareInterface
{
    /**
     * @param RequestInterface        $request
     * @param RequestHandlerInterface $requestHandler
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $response = $requestHandler->handle($request);

        $data             = $response->getData();
        $data['MethodMd'] = 'MethodMd';

        return $response->setData($data);
    }
}