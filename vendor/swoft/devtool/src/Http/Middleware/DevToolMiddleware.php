<?php declare(strict_types=1);

namespace Swoft\Devtool\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Config\Annotation\Mapping\Config;
use Swoft\Devtool\DevTool;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Contract\MiddlewareInterface;
use Swoft\Log\Helper\CLog;
use Throwable;
use function strpos;
use function view;
use const APP_DEBUG;

/**
 * Class DevToolMiddleware
 * @Bean()
 */
class DevToolMiddleware implements MiddlewareInterface
{
    /**
     * @Config("devtool.logHttpRequestToConsole")
     * @var bool
     */
    public $logHttpRequestToConsole = false;

    /**
     * @param ServerRequestInterface|Request $request
     * @param RequestHandlerInterface        $handler
     *
     * @return ResponseInterface
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uriPath    = $request->getUri()->getPath();
        $isAccessDt = 0 === strpos($uriPath, DevTool::ROUTE_PREFIX);

        // Must open debug on access devtool
        if ($isAccessDt && APP_DEBUG === 0) {
            return $handler->handle($request);
        }

        // Before request
        if ($this->logHttpRequestToConsole) {
            CLog::info('%s %s', $request->getMethod(), $uriPath);
        }

        // If it is not an ajax request, then render vue index file.
        if ($isAccessDt && !$request->isAjax()) {
            $json = $request->query('json');
            if (null === $json) {
                return view('@devtool/web/dist/index.html');
            }
        }

        $response = $handler->handle($request);

        // After request
        return $response->withAddedHeader('Swoft-DevTool-Version', '1.0.0');
    }
}
