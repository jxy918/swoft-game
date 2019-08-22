<?php declare(strict_types=1);

namespace Swoft\Tcp\Server;

use ReflectionException;
use ReflectionType;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Tcp\ErrCode;
use Swoft\Tcp\Package;
use Swoft\Tcp\Protocol;
use Swoft\Tcp\Server\Exception\CommandNotFoundException;
use Swoft\Tcp\Server\Exception\TcpUnpackingException;
use Swoft\Tcp\Server\Router\Router;
use Throwable;
use function server;
use function sprintf;

/**
 * Class TcpDispatcher
 *
 * @since 2.0.3
 * @Bean("tcpDispatcher")
 */
class TcpDispatcher
{
    /**
     * Enable internal route dispatching
     *
     * @see \Swoft\Tcp\Server\Swoole\ReceiveListener::onReceive()
     * @var bool
     */
    private $enable = true;

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws ReflectionException
     * @throws TcpUnpackingException
     * @throws CommandNotFoundException
     */
    public function dispatch(Request $request, Response $response): Response
    {
        /** @var Protocol $protocol */
        $protocol = Swoft::getBean('tcpServerProtocol');
        server()->log("Tcp protocol data packer is {$protocol->getPackerClass()}");

        try {
            $package = $protocol->unpack($request->getRawData());
        } catch (Throwable $e) {
            $errMsg = sprintf('unpack request data error - %s', $e->getMessage());
            throw new TcpUnpackingException($errMsg, ErrCode::UNPACKING_FAIL, $e);
        }

        /** @var Router $router */
        $router = Swoft::getBean('tcpRouter');
        $cmd    = $package->getCmd() ?: $router->getDefaultCommand();
        $request->setPackage($package);

        [$status, $info] = $router->match($cmd);
        if ($status === Router::NOT_FOUND) {
            $errMsg = sprintf("request command '%s' is not found of the tcp server", $cmd);
            throw new CommandNotFoundException($errMsg, ErrCode::ROUTE_NOT_FOUND);
        }

        [$ctlClass, $ctlMethod] = $info['handler'];

        server()->log("Tcp command: '{$cmd}', will call tcp request handler {$ctlClass}@{$ctlMethod}");

        $object = Swoft::getBean($ctlClass);
        $params = $this->getBindParams($ctlClass, $ctlMethod, $package, $request, $response);
        $result = $object->$ctlMethod(...$params);

        if ($result && !$result instanceof Response) {
            $response->setData($result);
        }

        return $response;
    }

    /**
     * Get method bounded params
     *
     * @param string  $class
     * @param string  $method
     * @param Package $package
     * @param Request $r
     * @param Response $w
     *
     * @return array
     * @throws ReflectionException
     */
    private function getBindParams(string $class, string $method, Package $package, Request $r, Response $w): array
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

            if ($type === Package::class) {
                $bindParams[] = $package;
            } elseif ($type === Request::class) {
                $bindParams[] = $r;
            } elseif ($type === Response::class) {
                $bindParams[] = $w;
            } else {
                $bindParams[] = null;
            }
        }

        return $bindParams;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable($enable): void
    {
        $this->enable = (bool)$enable;
    }
}
