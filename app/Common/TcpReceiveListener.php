<?php declare(strict_types=1);

namespace App\Common;

use ReflectionException;
use Swoft;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Server\Contract\ReceiveInterface;
use Swoft\Session\Session;
use Swoft\SwoftEvent;
use Swoft\Tcp\Server\Context\TcpReceiveContext;
use Swoft\Tcp\Server\Exception\TcpResponseException;
use Swoft\Tcp\Server\Request;
use Swoft\Tcp\Server\Response;
use Swoft\Tcp\Server\TcpErrorDispatcher;
use Swoft\Tcp\Server\TcpServerEvent;
use Swoole\Server;
use Throwable;

//自己定义的服务器逻辑
use App\WebSocket\Game\Core\Packet;
use App\WebSocket\Game\Core\Dispatch;
use App\WebSocket\Game\Core\Log;

/**
 * Class TcpReceiveListener
 *
 * @since 2.0
 * @Bean()
 */
class TcpReceiveListener implements ReceiveInterface
{
    /**
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     * @param string $data
     *
     * @throws ContainerException
     * @throws ReflectionException
     * @throws TcpResponseException
     */
    public function onReceive(Server $server, int $fd, int $reactorId, string $data): void
    {
        $response = Response::new($fd);
        $request  = Request::new($fd, $data, $reactorId);

        server()->log("Receive: conn#{$fd} received client request, begin init context", [], 'debug');
        $sid = (string)$fd;
        $ctx = TcpReceiveContext::new($fd, $request, $response);

        // Storage context
        Context::set($ctx);
        Session::bindCo($sid);

        try {
            Log::show(" Receive: client #{$fd} send success Mete: \n{");
            if(!empty($data)) {
                $data = Packet::packDecode($data);
                $back = '';
                if (isset($data['code']) && $data['code'] == 0 && isset($data['msg']) && $data['msg'] == 'OK') {
                    Log::show('Recv <<<  cmd=' . $data['cmd'] . '  scmd=' . $data['scmd'] . '  len=' . $data['len'] . '  data=' . json_encode($data['data']));
                    //转发请求，代理模式处理,websocket路由到相关逻辑
                    $data['serv'] = $server;
                    $back = $this->dispatch($data);
                    Log::show('Tcp Strategy <<<  data=' . $back);
                } else {
                    Log::show($data['msg']);
                }
                if (!empty($back)) {
                    $server->send($fd, $back);
                }
            }
            Log::split('}');
        } catch (Throwable $e) {
            server()->log("Receive: conn#{$fd} error: " . $e->getMessage(), [], 'error');
            Swoft::trigger(TcpServerEvent::RECEIVE_ERROR, $e, $fd);

            /** @var TcpErrorDispatcher $errDispatcher */
            $errDispatcher = Swoft::getSingleton(TcpErrorDispatcher::class);

            // Dispatching error handle
            $response = $errDispatcher->receiveError($e, $response);
            $response->send($server);
        } finally {
            // Defer
            Swoft::trigger(SwoftEvent::COROUTINE_DEFER);

            // Destroy
            Swoft::trigger(SwoftEvent::COROUTINE_COMPLETE);

            // Unbind cid => sid(fd)
            Session::unbindCo();
        }
    }

    /**
     * 根据路由策略处理逻辑，并返回数据
     * @param $data
     * @return string
     */
    public function dispatch($data) {
        $obj = new Dispatch($data);
        $back = "<center><h1>404 Not Found </h1></center><hr><center>swoft</center>\n";
        if(!empty($obj->getStrategy())) {
            $back = $obj->exec();
        }
        return $back;
    }
}
