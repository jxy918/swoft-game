<?php declare(strict_types=1);

namespace Swoft\Http\Server;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Concern\AbstractDispatcher;
use Swoft\Exception\SwoftException;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Formatter\AcceptResponseFormatter;
use Swoft\Http\Server\Middleware\DefaultMiddleware;
use Swoft\Http\Server\Middleware\UserMiddleware;
use Swoft\Http\Server\Middleware\ValidatorMiddleware;
use Swoft\Http\Server\Router\Router;
use Throwable;

/**
 * Class HttpDispatcher
 *
 * @Bean("httpDispatcher")
 * @since 2.0
 */
class HttpDispatcher extends AbstractDispatcher
{
    /**
     * Default middleware to handler request
     *
     * @var string
     */
    protected $defaultMiddleware = DefaultMiddleware::class;

    /**
     * 1 pre-match before run middleware
     * 2 normal match on UserMiddleware
     * @var int
     */
    // private $routeMatchStrategy = 2;

    /**
     * Accept formatter
     *
     * @var AcceptResponseFormatter
     * @Inject()
     */
    protected $acceptFormatter;

    /**
     * Dispatch http request
     *
     * @param mixed ...$params
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function dispatch(...$params): void
    {
        /**
         * @var Request  $request
         * @var Response $response
         */
        [$request, $response] = $params;

        /* @var RequestHandler $requestHandler */
        $requestHandler = Swoft::getBean(RequestHandler::class);
        $requestHandler->initialize($this->requestMiddleware(), $this->defaultMiddleware);

        try {
            // Trigger before handle event
            Swoft::trigger(HttpServerEvent::BEFORE_REQUEST, null, $request, $response);

            // Match router and handle
            $request  = $this->matchRouter($request);
            $response = $requestHandler->handle($request);
        } catch (Throwable $e) {
            /** @var HttpErrorDispatcher $errDispatcher */
            $errDispatcher = Swoft::getSingleton(HttpErrorDispatcher::class);

            // Handle request error
            $response = $errDispatcher->run($e, $response);
        }

        // Format response content type
        $response = $this->acceptFormatter->format($response);

        // Trigger after request
        Swoft::trigger(HttpServerEvent::AFTER_REQUEST, null, $response);
    }

    /**
     * @return array
     */
    public function afterMiddleware(): array
    {
        return [
            UserMiddleware::class
        ];
    }

    /**
     * @param Request $request
     *
     * @return Request
     * @throws ContainerException
     * @throws SwoftException
     */
    private function matchRouter(Request $request): Request
    {
        /** @var Request $request $method */
        $method  = $request->getMethod();
        $uriPath = $request->getUriPath();

        /** @var Router $router */
        $router = Swoft::getSingleton('httpRouter');
        $result = $router->match($uriPath, $method);

        // Save matched route data to request
        $request = $request->withAttribute(Request::ROUTER_ATTRIBUTE, $result);
        context()->setRequest($request);

        return $request;
    }
}
