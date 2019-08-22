<?php declare(strict_types=1);

namespace Swoft\Devtool\Http\Controller;

use ReflectionException;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;

/**
 * Class RouteController
 *
 * @Controller("/__devtool/rpc")
 */
class RpcController
{
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

        /** @var \Swoft\Rpc\Server\Router\Router $router */
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
