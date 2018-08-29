<?php
/**
 * @uses      RouterTrait
 * @version   2018年01月28日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2018 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */

namespace Swoft\Http\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Swoft\App;
use Swoft\Http\Server\AttributeEnum;

/**
 * Trait RouterTrait
 * @package Swoft\Http\Server\Middleware
 */
trait RouterTrait
{
    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    protected function handleRouter(ServerRequestInterface $request): ServerRequestInterface
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        /* @var \Swoft\Http\Server\Router\HandlerMapping $httpRouter */
        $httpRouter = App::getBean('httpRouter');
        $httpHandler = $httpRouter->getHandler($path, $method);
        $request = $request->withAttribute(AttributeEnum::ROUTER_ATTRIBUTE, $httpHandler);

        return $request;
    }

}
