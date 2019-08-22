<?php declare(strict_types=1);


namespace Swoft\Rpc\Client;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Packet;
use Swoft\Rpc\Packet\SwoftPacketV1;
use Swoft\SwoftComponent;

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
        return [];
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function beans(): array
    {
        return [
            'rpcClientPacket'        => [
                'class' => Packet::class
            ],
            'rpcClientSwoftPacketV1' => [
                'class'   => Packet::class,
                'packets' => [
                    'swoftV1' => bean(SwoftPacketV1::class)
                ],
                'type'    => 'swoftV1',
                'packageEof' => "\r\n",
            ]
        ];
    }
}