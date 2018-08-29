<?php

namespace Swoft\View\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Contract\Arrayable;
use Swoft\Core\RequestContext;
use Swoft\Http\Server\AttributeEnum;
use Swoft\Http\Server\Middleware\AcceptMiddleware;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\View\Bean\Collector\ViewCollector;

/**
 * The middleware of view
 *
 * @Bean()
 */
class ViewMiddleware implements MiddlewareInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface     $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $response = $this->responseView($request, $response);

        return $response;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     *
     * @return \Psr\Http\Message\ResponseInterface|\Swoft\Http\Message\Server\Response
     */
    private function responseView(ServerRequestInterface $request, ResponseInterface $response)
    {
        // the info of view model
        $collector        = ViewCollector::getCollector();
        $controllerClass  = RequestContext::getContextDataByKey('controllerClass');
        $controllerAction = RequestContext::getContextDataByKey('controllerAction');
        $template         = $collector[$controllerClass]['view'][$controllerAction]['template'] ?? "";
        $layout           = $collector[$controllerClass]['view'][$controllerAction]['layout'] ?? "";

        // accept and the of response
        $accepts       = $request->getHeader('accept');
        $currentAccept = current($accepts);

        /* @var \Swoft\Http\Message\Server\Response $response */
        $responseAttribute = AttributeEnum::RESPONSE_ATTRIBUTE;
        $data = $response->getAttribute($responseAttribute);

        // the condition of view
        $isTextHtml = !empty($currentAccept) && $response->isMatchAccept($currentAccept, 'text/html');
        $isTempalte = $controllerClass && $response->isArrayable($data) && $template;

        // show view
        if ($isTextHtml && $isTempalte) {
            if ($data instanceof Arrayable) {
                $data = $data->toArray();
            }

            /* @var \Swoft\View\Base\View $view */
            $view    = App::getBean('view');
            $content = $view->render($template, $data, $layout);
            $response = $response->withContent($content)->withAttribute($responseAttribute, null);
            $response = $response->withoutHeader('Content-Type')->withAddedHeader('Content-Type', 'text/html');
        }

        return $response;
    }
}
