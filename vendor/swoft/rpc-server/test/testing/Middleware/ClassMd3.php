<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Testing\Middleware;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Rpc\Server\Contract\MiddlewareInterface;
use Swoft\Rpc\Server\Contract\RequestHandlerInterface;
use Swoft\Rpc\Server\Contract\RequestInterface;
use Swoft\Rpc\Server\Contract\ResponseInterface;

/**
 * Class ClassMd3
 *
 * @since 2.0
 *
 * @Bean()
 */
class ClassMd3 implements MiddlewareInterface
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
        $data['ClassMd3'] = 'ClassMd3';

        return $response->setData($data);
    }
}