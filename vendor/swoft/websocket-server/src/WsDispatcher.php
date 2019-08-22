<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server;

use InvalidArgumentException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Session\Session;
use Swoft\WebSocket\Server\Contract\WsModuleInterface;
use Swoft\WebSocket\Server\Exception\WsModuleRouteException;
use Swoft\WebSocket\Server\Router\Router;
use Swoole\WebSocket\Server;
use Throwable;
use function server;
use function sprintf;

/**
 * Class WsDispatcher
 *
 * @since 1.0
 * @Bean("wsDispatcher")
 */
class WsDispatcher
{
    /**
     * Dispatch handshake request
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return array eg. [status, response]
     * @throws WsModuleRouteException
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function handshake(Request $request, Response $response): array
    {
        /** @var Router $router */
        $router = Swoft::getSingleton('wsRouter');

        /** @var Connection $conn */
        $conn = Session::mustGet();
        $path = $request->getUriPath();

        if (!$info = $router->match($path)) {
            throw new WsModuleRouteException(sprintf('The requested websocket route path "%s" is not exist!', $path));
        }

        $class = $info['class'];
        $conn->setModuleInfo($info);

        server()->log("Handshake: found handler for path '$path', ws module class is $class", [], 'debug');

        /** @var WsModuleInterface $module */
        $module = Swoft::getSingleton($class);

        // Call user method
        if ($method = $info['eventMethods']['handshake'] ?? '') {
            return $module->$method($request, $response);
        }

        // Auto handshake
        return [true, $response->withAddedHeader('swoft-ws-handshake', 'auto')];
    }

    /**
     * Dispatch ws close handle
     *
     * @param Server $server
     * @param int    $fd
     */
    public function close(Server $server, int $fd): void
    {
        /** @var Connection $conn */
        $conn = Session::mustGet();
        $info = $conn->getModuleInfo();

        $method = $info['eventMethods']['close'] ?? '';
        if (!$method) {
            return;
        }

        $class = $info['class'];
        server()->log("conn#{$fd} call ws close handler '{$class}::{$method}'", [], 'debug');

        /** @var WsModuleInterface $module */
        $module = Swoft::getSingleton($class);
        $module->$method($server, $fd);
    }
}
