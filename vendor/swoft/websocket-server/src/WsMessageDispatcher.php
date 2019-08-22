<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server;

use ReflectionException;
use ReflectionType;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Session\Session;
use Swoft\WebSocket\Server\Contract\WsModuleInterface;
use Swoft\WebSocket\Server\Exception\WsMessageParseException;
use Swoft\WebSocket\Server\Exception\WsMessageRouteException;
use Swoft\WebSocket\Server\Message\Message;
use Swoft\WebSocket\Server\Message\Request;
use Swoft\WebSocket\Server\Message\Response;
use Swoft\WebSocket\Server\Router\Router;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use Throwable;
use function server;

/**
 * Class WsMessageDispatcher
 *
 * @since 2.0
 *
 * @Bean("wsMsgDispatcher")
 */
class WsMessageDispatcher // extends \Swoft\Concern\AbstractDispatcher
{
    /**
     * Dispatch ws message handle
     *
     * @param Server   $server
     * @param Request  $request
     * @param Response $response
     *
     * @throws ReflectionException
     * @throws Swoft\Exception\SwoftException
     * @throws WsMessageParseException
     * @throws WsMessageRouteException
     */
    public function dispatch(Server $server, Request $request, Response $response): void
    {
        $fd = $request->getFd();

        /** @var Connection $conn */
        $conn  = Session::mustGet();
        $info  = $conn->getModuleInfo();
        $frame = $request->getFrame();

        // Want custom message handle, will don't trigger message parse and dispatch.
        if ($method = $info['eventMethods']['message'] ?? '') {
            $class = $info['class'];
            server()->log("Message: conn#{$fd} call custom message handler '{$class}::{$method}'", [], 'debug');

            /** @var WsModuleInterface $module */
            $module = Swoft::getSingleton($class);
            $module->$method($server, $frame);
            return;
        }

        // Parse message data and dispatch route handle
        try {
            $parser  = $conn->getParser();
            $message = $parser->decode($frame->data);
        } catch (Throwable $e) {
            throw new WsMessageParseException("parse message error '{$e->getMessage()}", 500, $e);
        }

        // Set Message to request
        $request->setMessage($message);

        /** @var Router $router */
        $cmdId  = $message->getCmd() ?: $info['defaultCommand'];
        $router = Swoft::getSingleton('wsRouter');

        [$status, $route] = $router->matchCommand($info['path'], $cmdId);
        if ($status === Router::NOT_FOUND) {
            throw new WsMessageRouteException("message command '$cmdId' is not found, in module {$info['path']}");
        }

        [$ctlClass, $ctlMethod] = $route['handler'];
        server()->log(
            "Message: conn#{$fd} call message command handler '{$ctlClass}::{$ctlMethod}'",
            $message->toArray(),
            'debug'
        );

        $object = Swoft::getBean($ctlClass);
        $params = $this->getBindParams($ctlClass, $ctlMethod, $request, $response);
        $result = $object->$ctlMethod(...$params);

        if ($result && $result instanceof Response) {
            $response = $result;
        } elseif ($result !== null) {
            // Set user data and change default opcode
            $response->setData($result);
            $response->setOpcode((int)$route['opcode']);
        }

        // Before send message
        Swoft::trigger(WsServerEvent::MESSAGE_SEND, $response);

        // Do send response
        $response->send();
    }

    /**
     * Get method bounded params
     *
     * @param string   $class
     * @param string   $method
     * @param Request  $request
     * @param Response $response
     *
     * @return array
     * @throws ReflectionException
     */
    private function getBindParams(string $class, string $method, Request $request, Response $response): array
    {
        $classInfo = Swoft::getReflection($class);
        if (!isset($classInfo['methods'][$method])) {
            return [];
        }

        // binding params
        $bindParams   = [];
        $methodParams = $classInfo['methods'][$method]['params'];

        /**
         * @var string         $name
         * @var ReflectionType $paramType
         * @var mixed          $devVal
         */
        foreach ($methodParams as [$name, $paramType, $devVal]) {
            // Defined type of the param
            $type = $paramType ? $paramType->getName() : '';

            if ($type === '' && $name === 'data') {
                $bindParams[] = $request->getRawData();
            } elseif ($type === Frame::class) {
                $bindParams[] = $request->getFrame();
            } elseif ($type === Message::class) {
                $bindParams[] = $request->getMessage();
            } elseif ($type === Request::class) {
                $bindParams[] = $request;
            } elseif ($type === Response::class) {
                $bindParams[] = $response;
            } else {
                $bindParams[] = null;
            }
        }

        return $bindParams;
    }
}
