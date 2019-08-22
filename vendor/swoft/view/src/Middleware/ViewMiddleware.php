<?php declare(strict_types=1);

namespace Swoft\View\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\ContentType;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Http\Server\Router\Route;
use Swoft\Http\Server\Router\Router;
use Swoft\Stdlib\Contract\Arrayable;
use Swoft\View\Renderer;
use Swoft\View\ViewRegister;
use Throwable;
use function context;
use function current;
use function is_object;
use function strpos;

/**
 * Class ViewMiddleware - The middleware of view render
 *
 * @Bean()
 */
class ViewMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Response $response */
        $response = $handler->handle($request);

        /* @var Route $route */
        [$status, , $route] = context()->getRequest()->getAttribute(Request::ROUTER_ATTRIBUTE);
        if ($status !== Router::FOUND) {
            return $response;
        }

        $actionId = $route->getHandler();
        if (!$info = ViewRegister::findBindView($actionId)) {
            return $response;
        }

        // Get layout and template
        [$template, $layout] = $info;

        // Accept list
        $allowedAccepts = $request->getHeader('accept');
        $currentAccept  = current($allowedAccepts);
        $contentType    = ContentType::HTML;

        if ($template && false !== strpos($currentAccept, $contentType)) {
            $data = $response->getData();

            if (is_object($data) && $data instanceof Arrayable) {
                $data = $data->toArray();
            }

            /* @var Renderer $view */
            $renderer = \bean('view');
            $content  = $renderer->render($template, $data, $layout);

            return $response->withContent($content)->withContentType(ContentType::KEY, $contentType);
        }

        return $response;
    }
}
