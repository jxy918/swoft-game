<?php declare(strict_types=1);


namespace Swoft\Rpc\Server\Swoole;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Exception\RpcException;
use Swoft\Rpc\Server\Request;
use Swoft\Rpc\Server\Response;
use Swoft\Rpc\Server\ServiceDispatcher;
use Swoft\Server\Contract\ReceiveInterface;
use Swoole\Server;

/**
 * Class ReceiveListener
 *
 * @since 2.0
 *
 * @Bean()
 */
class ReceiveListener implements ReceiveInterface
{
    /**
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     * @param string $data
     *
     * @throws ReflectionException
     * @throws ContainerException
     * @throws RpcException
     */
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): void
    {
        $request  = Request::new($server, $fd, $reactorId, $data);
        $response = Response::new($server, $fd, $reactorId);

        /* @var ServiceDispatcher $dispatcher */
        $dispatcher = BeanFactory::getSingleton('serviceDispatcher');

        $dispatcher->dispatch($request, $response);
    }
}