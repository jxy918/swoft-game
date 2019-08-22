<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Testing\Middleware;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Rpc\Server\Contract\MiddlewareInterface;
use Swoft\Rpc\Server\Contract\RequestHandlerInterface;
use Swoft\Rpc\Server\Contract\RequestInterface;
use Swoft\Rpc\Server\Contract\ResponseInterface;

/**
 * Class ClassMd2
 *
 * @since 2.0
 *
 * @Bean()
 */
class ClassMd2 implements MiddlewareInterface
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
        $data['ClassMd2'] = 'ClassMd2';

        return $response->setData($data);
    }
}