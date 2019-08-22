<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Testing\Middleware;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Rpc\Server\Contract\MiddlewareInterface;
use Swoft\Rpc\Server\Contract\RequestHandlerInterface;
use Swoft\Rpc\Server\Contract\RequestInterface;
use Swoft\Rpc\Server\Contract\ResponseInterface;

/**
 * Class UserMd
 *
 * @since 2.0
 *
 * @Bean()
 */
class UserMd implements MiddlewareInterface
{
    public function process(RequestInterface $request, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        if ($request->getVersion() != '1.3') {
            return $requestHandler->handle($request);
        }

        $response = $requestHandler->handle($request);

        $data           = $response->getData();
        $data['userMd'] = 'userMd';

        return $response->setData($data);
    }
}