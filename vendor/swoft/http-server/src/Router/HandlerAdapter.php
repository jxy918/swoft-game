<?php

namespace Swoft\Http\Server\Router;

use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Core\RequestContext;
use Swoft\Exception\InvalidArgumentException;
use Swoft\Helper\JsonHelper;
use Swoft\Helper\PhpHelper;
use Swoft\Helper\StringHelper;
use Swoft\Http\Message\Router\HandlerAdapterInterface;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Server\Response;
use Swoft\Http\Server\AttributeEnum;
use Swoft\Http\Server\Exception\MethodNotAllowedException;
use Swoft\Http\Server\Exception\RouteNotFoundException;
use Swoft\Http\Server\Payload;

/**
 * @Bean("httpHandlerAdapter")
 */
class HandlerAdapter implements HandlerAdapterInterface
{
    /**
     * Execute handler with controller and action
     *
     * @param ServerRequestInterface $request request object
     * @param array $routeInfo handler info
     * @return Response
     * @throws \Swoft\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \Swoft\Http\Server\Exception\MethodNotAllowedException
     * @throws \Swoft\Http\Server\Exception\RouteNotFoundException
     * @throws \ReflectionException
     */
    public function doHandler(ServerRequestInterface $request, array $routeInfo)
    {
        /**
         * @var int $status
         * @var string $path
         * @var array  $info
         */
        list($status, $path, $info) = $routeInfo;

        // not founded route
        if ($status === HandlerMapping::NOT_FOUND) {
            throw new RouteNotFoundException('Route not found for ' . $path);
        }

        // method not allowed
        if ($status === HandlerMapping::METHOD_NOT_ALLOWED) {
            throw new MethodNotAllowedException(sprintf(
                "Method '%s' not allowed for access %s, Allow: %s",
                $request->getMethod(),
                $path,
                \implode(',', $routeInfo[2])
            ));
        }

        // handler info
        list($handler, $matches) = $this->createHandler($path, $info);

        if (\is_array($handler)) {
            $handler = $this->defaultHandler($handler);
        }

        // execute handler
        $params   = $this->bindParams($request, $handler, $matches);
        $response = PhpHelper::call($handler, $params);

        // response
        if (!$response instanceof Response) {
            /* @var Response $newResponse*/
            $newResponse = RequestContext::getResponse();

            // if is Payload
            if ($response instanceof Payload) {
                $response = $newResponse
                    ->withStatus($response->getStatus())
                    ->withAttribute(AttributeEnum::RESPONSE_ATTRIBUTE , $response->data);
            } else {
                $response = $newResponse->withAttribute(AttributeEnum::RESPONSE_ATTRIBUTE , $response);
            }
        }

        return $response;
    }

    /**
     * Create handler
     *
     * @param string $path url path
     * @param array  $info path info
     * @return array
     * @throws \InvalidArgumentException
     */
    public function createHandler(string $path, array $info): array
    {
        $handler = $info['handler'];
        $matches = $info['matches'] ?? [];

        // is a \Closure or a callable object
        if (\is_object($handler)) {
            return [$handler, $matches];
        }

        // is array ['controller', 'action']
        if (\is_array($handler)) {
            $segments = $handler;
        } elseif (\is_string($handler)) {
            // e.g `Controllers\Home@index` Or only `Controllers\Home`
            $segments = explode('@', trim($handler));
        } else {
            App::error('Invalid route handler for URI: ' . $path);
            throw new \InvalidArgumentException('Invalid route handler for URI: ' . $path);
        }

        $action    = '';
        $className = $segments[0];
        if (isset($segments[1])) {
            // Already assign action
            $action = $segments[1];
        } elseif (isset($matches[0])) {
            // use dynamic action
            $action = array_shift($matches);
        }

        $action     = HandlerMapping::convertNodeStr($action);
        $controller = App::getBean($className);
        $handler    = [$controller, $action];

        // Set Controller and Action info to Request Context
        RequestContext::setContextData([
            'controllerClass'  => $className,
            'controllerAction' => $action ?: 'index',
        ]);

        return [$handler, $matches];
    }

    /**
     * @param array $handler handler info
     * @return array
     * @throws \Swoft\Exception\InvalidArgumentException
     */
    private function defaultHandler(array $handler): array
    {
        list($controller, $actionId) = $handler;
        $httpRouter = App::getBean('httpRouter');

        $actionId = empty($actionId) ? $httpRouter->defaultAction : $actionId;
        if (!method_exists($controller, $actionId)) {
            throw new InvalidArgumentException("the {$actionId} of action is not exist!");
        }

        return [$controller, $actionId];
    }

    /**
     * Binding params of action method
     *
     * @param ServerRequestInterface $request request object
     * @param mixed $handler handler
     * @param array $matches route params info
     * @return array
     * @throws \ReflectionException
     */
    private function bindParams(ServerRequestInterface $request, $handler, array $matches): array
    {
        if (\is_array($handler)) {
            list($controller, $method) = $handler;
            $reflectMethod = new \ReflectionMethod($controller, $method);
            $reflectParams = $reflectMethod->getParameters();
        } else {
            $reflectMethod = new \ReflectionFunction($handler);
            $reflectParams = $reflectMethod->getParameters();
        }

        $bindParams = [];

        // Binding params
        foreach ($reflectParams as $key => $reflectParam) {
            $reflectType = $reflectParam->getType();
            $name        = $reflectParam->getName();

            // undefined type of the param
            if ($reflectType === null) {
                if (isset($matches[$name])) {
                    $bindParams[$key] = $matches[$name];
                } else {
                    $bindParams[$key] = null;
                }
                continue;
            }

            /**
             * Defined type of the param
             * @notice \ReflectType::getName() is not supported in PHP 7.0, that is why use __toString()
             */
            $type = $reflectType->__toString();
            if ($type === Request::class) {
                // Current Request Object
                $bindParams[$key] = $request;
            } elseif ($type === Response::class) {
                // Current Response Object
                $bindParams[$key] = RequestContext::getResponse();
            } elseif (isset($matches[$name])) {
                // Request parameters
                $bindParams[$key] = $this->parserParamType($type, $matches[$name]);
            } elseif (App::hasBean($type)) {
                // Bean
                $bindParams[$key] = App::getBean($type);
            } elseif (\class_exists($type)) {
                // Class
                $bindParams[$key] = $this->bindRequestParamsToClass($request, new \ReflectionClass($type));
            } else {
                $bindParams[$key] = $this->getDefaultValue($type);
            }
        }

        return $bindParams;
    }

    /**
     * Bind request parameters to instance of ReflectClass
     *
     * @param ServerRequestInterface $request
     * @param \ReflectionClass $reflectClass
     * @return Object
     */
    private function bindRequestParamsToClass(ServerRequestInterface $request, \ReflectionClass $reflectClass)
    {
        try {
            $object = $reflectClass->newInstance();
            $queryParams = $request->getQueryParams();
            // Get request body, auto decode when content type is json format
            if (StringHelper::startsWith($request->getHeaderLine('Content-Type'), 'application/json')) {
                $requestBody = JsonHelper::decode($request->getBody()->getContents(), true);
            } else {
                $requestBody = $request->getParsedBody();
            }
            // Merge query params and request body
            $requestParams = array_merge($queryParams, $requestBody);
            // Binding request params to target object
            $properties = $reflectClass->getProperties();
            foreach ($properties as $property) {
                $name = $property->getName();
                if (!isset($requestParams[$name])) {
                    continue;
                }
                if (!$property->isPublic()) {
                    $property->setAccessible(true);
                }
                $property->setValue($object, $requestParams[$name]);
            }
        } catch (\Exception $e) {
            $object = null;
        }
        return $object;
    }

    /**
     * parser the type of binding param
     *
     * @param string $type  the type of param
     * @param mixed  $value the value of param
     *
     * @return bool|float|int|string
     */
    private function parserParamType(string $type, $value)
    {
        switch ($type) {
            case 'int':
                $value = (int)$value;
                break;
            case 'string':
                $value = (string)$value;
                break;
            case 'bool':
                $value = (bool)$value;
                break;
            case 'float':
                $value = (float)$value;
                break;
            case 'double':
                $value = (double)$value;
                break;
        }

        return $value;
    }

    /**
     * the default value of param
     *
     * @param string $type the type of param
     *
     * @return bool|float|int|string
     */
    private function getDefaultValue(string $type)
    {
        $value = null;
        switch ($type) {
            case 'int':
                $value = 0;
                break;
            case 'string':
                $value = '';
                break;
            case 'bool':
                $value = false;
                break;
            case 'float':
                $value = 0;
                break;
            case 'double':
                $value = 0;
                break;
        }

        return $value;
    }
}
