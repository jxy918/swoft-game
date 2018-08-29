<?php

namespace Swoft\Devtool\Controller;

use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;

/**
 * Class RouteController
 * @package Swoft\Devtool\Controller
 * @Controller("/__devtool")
 */
class RouteController
{
    /**
     * @RequestMapping("http/routes", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     */
    public function httpRoutes(Request $request): array
    {
        $type = $request->query('type');
        $types = [
            'static' => 1,
            'regular' => 1,
            'vague' => 1,
            'cached' => 1,
        ];

        /** @var \Swoft\Http\Server\Router\HandlerMapping $router */
        $router = \bean('httpRouter');

        // one type
        if (isset($types[$type])) {
            $getter = 'get' . \ucfirst($type) . 'Routes';

            return $router->$getter();
        }

        // all
        return [
            'static' => $router->getStaticRoutes(),
            'regular' => $router->getRegularRoutes(),
            'vague' => $router->getVagueRoutes(),
            'cached' => $router->getCacheRoutes(),
        ];
    }

    /**
     * @RequestMapping("ws/routes", method=RequestMethod::GET)
     * @return array
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
     */
    public function rpcRoutes(): array
    {
        if (!BeanFactory::hasBean('serviceRouter')) {
            return [];
        }

        /** @var \Swoft\Rpc\Server\Router\HandlerMapping $router */
        $router = \bean('serviceRouter');
        $rawList = $router->getRoutes();
        $routes = [];

        foreach ($rawList as $key => $route) {
            $routes[] = [
                'serviceKey' => $key,
                'class' => $route[0],
                'method' => $route[1],
            ];
        }

        return $routes;
    }
}
