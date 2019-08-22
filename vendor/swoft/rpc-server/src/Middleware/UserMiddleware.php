<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Middleware;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Server\Contract\MiddlewareInterface;
use Swoft\Rpc\Server\Contract\RequestHandlerInterface;
use Swoft\Rpc\Server\Contract\RequestInterface;
use Swoft\Rpc\Server\Contract\ResponseInterface;
use Swoft\Rpc\Server\Exception\RpcServerException;
use Swoft\Rpc\Server\Request;
use Swoft\Rpc\Server\Router\Router;
use Swoft\Rpc\Server\ServiceHandler;

/**
 * Class UserMiddleware
 *
 * @since 2.0
 *
 * @Bean()
 */
class UserMiddleware implements MiddlewareInterface
{
    /**
     * @param RequestInterface        $request
     * @param RequestHandlerInterface $requestHandler
     *
     * @return ResponseInterface
     * @throws ReflectionException
     * @throws ContainerException
     * @throws RpcServerException
     */
    public function process(RequestInterface $request, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $version   = $request->getVersion();
        $interface = $request->getInterface();
        $method    = $request->getMethod();

        /* @var Router $router */
        $router = BeanFactory::getBean('serviceRouter');

        $handler = $router->match($version, $interface);
        $request->setAttribute(Request::ROUTER_ATTRIBUTE, $handler);

        [$status, $className] = $handler;

        if ($status != Router::FOUND) {
            return $requestHandler->handle($request);
        }

        $middlewares = MiddlewareRegister::getMiddlewares($className, $method);
        if (!empty($middlewares) && $requestHandler instanceof ServiceHandler) {
            $requestHandler->insertMiddlewares($middlewares);
        }

        return $requestHandler->handle($request);
    }
}