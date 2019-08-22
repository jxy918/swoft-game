<?php declare(strict_types=1);

namespace Swoft\Devtool\Http\Controller;

use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;

/**
 * Class RouteController
 *
 * @Controller("/__devtool")
 */
class RouteController
{
    /**
     * @RequestMapping("http/routes", method=RequestMethod::GET)
     * @param Request $request
     * @return array|string
     * @throws \Throwable
     */
    public function httpRoutes(Request $request)
    {
        $asString = (int)$request->query('asString', 0);

        /** @var \Swoft\Http\Server\Router\Router $router */
        $router = \bean('httpRouter');

        if ($asString === 1) {
            return $router->toString();
        }

        return [
            'routes'  => $router->getRoutes()
        ];
    }

    /**
     * @RequestMapping("ws/routes", method=RequestMethod::GET)
     * @return array
     * @throws \Throwable
     */
    public function wsRoutes(): array
    {
        if (!BeanFactory::hasBean('wsRouter')) {
            return [];
        }

        /** @var \Swoft\WebSocket\Server\Router\HandlerMapping $router */
        $router = \bean('wsRouter');

        return $router->getRoutes();
    }

    /**
     * @RequestMapping("rpc/routes", method=RequestMethod::GET)
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function rpcRoutes(): array
    {
        if (!BeanFactory::hasBean('serviceRouter')) {
            return [];
        }

        /** @var \Swoft\Rpc\Server\Router\HandlerMapping $router */
        $router  = \bean('serviceRouter');
        $rawList = $router->getRoutes();
        $routes  = [];

        foreach ($rawList as $key => $route) {
            $routes[] = [
                'serviceKey' => $key,
                'class'      => $route[0],
                'method'     => $route[1],
            ];
        }

        return $routes;
    }
}
