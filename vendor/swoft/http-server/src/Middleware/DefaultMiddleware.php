<?php declare(strict_types=1);

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use ReflectionNamedType;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Container;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Http\Server\Exception\MethodNotAllowedException;
use Swoft\Http\Server\Exception\NotFoundRouteException;
use Swoft\Http\Server\Formatter\AcceptResponseFormatter;
use Swoft\Http\Server\Router\Route;
use Swoft\Http\Server\Router\Router;
use Swoft\Stdlib\Helper\ObjectHelper;
use Swoft\Stdlib\Helper\PhpHelper;
use function context;
use function explode;
use function sprintf;

/**
 * Class DefaultMiddleware
 *
 * @Bean()
 * @since 2.0
 */
class DefaultMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface|Request  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws MethodNotAllowedException
     * @throws NotFoundRouteException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method  = $request->getMethod();
        $uriPath = $request->getUriPath();

        /* @var Route $route */
        [$status, , $route] = $request->getAttribute(Request::ROUTER_ATTRIBUTE);

        // Not found
        if ($status === Router::NOT_FOUND) {
            throw new NotFoundRouteException("Route not found(path {$uriPath})!");
        }

        // Method not allowed
        if ($status === Router::METHOD_NOT_ALLOWED) {
            throw new MethodNotAllowedException(sprintf('Uri(%s) method(%s) not allowed!', $uriPath, $method));
        }

        // Controller and method
        $handlerId = $route->getHandler();
        [$className, $method] = explode('@', $handlerId);

        // Update context request
        context()->setRequest($request);

        $pathParams = $route->getParams();
        $bindParams = $this->bindParams($className, $method, $pathParams);
        $controller = Container::$instance->getSingleton($className);

        // Call class method
        $data = PhpHelper::call([$controller, $method], ...$bindParams);

        // Return is instanceof `ResponseInterface`
        if ($data instanceof ResponseInterface) {
            return $data;
        }

        $response = context()->getResponse();
        return $response->withData($data);
    }

    /**
     * Bind params
     *
     * @param string $className
     * @param string $method
     * @param array  $pathParams
     *
     * @return array
     * @throws ReflectionException
     */
    private function bindParams(string $className, string $method, array $pathParams): array
    {
        $reflection   = Swoft::getReflection($className);
        $methodParams = $reflection['methods'][$method]['params'] ?? [];
        if (!$methodParams) {
            return [];
        }

        $bindParams = [];
        foreach ($methodParams as $methodParam) {
            [$paramName, $paramType, $paramDefaultType] = $methodParam;
            if (!$paramType instanceof ReflectionNamedType) {
                continue;
            }

            $type = $paramType->getName();
            if ($type === Request::class) {
                $bindParams[] = context()->getRequest();
            } elseif ($type === Response::class) {
                $bindParams[] = context()->getResponse();
            } elseif (isset($pathParams[$paramName])) {
                $bindParams[] = ObjectHelper::parseParamType($type, $pathParams[$paramName]);
            } else {
                $bindParams[] = $paramDefaultType ?? ObjectHelper::getDefaultValue($type);
            }
        }

        return $bindParams;
    }
}
