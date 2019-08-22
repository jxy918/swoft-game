<?php declare(strict_types=1);

namespace Swoft\Rpc\Server;

use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Helper\ComposerJSON;
use Swoft\Rpc\Packet;
use Swoft\Rpc\Server\Swoole\CloseListener;
use Swoft\Rpc\Server\Swoole\ConnectListener;
use Swoft\Rpc\Server\Swoole\ReceiveListener;
use Swoft\Server\SwooleEvent;
use Swoft\SwoftComponent;
use function bean;

/**
 * Class AutoLoader
 *
 * @since 2.0
 */
class AutoLoader extends SwoftComponent
{
    /**
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * @return array
     */
    public function metadata(): array
    {
        $jsonFile = dirname(__DIR__) . '/composer.json';

        return ComposerJSON::open($jsonFile)->getMetadata();
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function beans(): array
    {
        return [
            'rpcServer' => [
                'class' => ServiceServer::class,
                'on'    => [
                    SwooleEvent::CONNECT => bean(ConnectListener::class),
                    SwooleEvent::CLOSE   => bean(CloseListener::class),
                    SwooleEvent::RECEIVE => bean(ReceiveListener::class),
                ]
            ],
            'rpcServerPacket' => [
                'class' => Packet::class
            ]
        ];
    }
}
