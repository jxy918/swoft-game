<?php declare(strict_types=1);


namespace Swoft\Rpc;


use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Contract\PacketInterface;
use Swoft\Rpc\Exception\RpcException;
use Swoft\Rpc\Packet\AbstractPacket;
use Swoft\Rpc\Packet\JsonPacket;
use Swoft\Stdlib\Helper\Arr;

/**
 * Class Packet
 *
 * @since 2.0
 */
class Packet implements PacketInterface
{
    /**
     * Json packet
     */
    const JSON = 'JSON';

    /**
     * Packet type
     *
     * @var string
     */
    private $type = self::JSON;

    /**
     * Packet
     */
    private $packets = [];

    /**
     * @var bool
     */
    private $openEofCheck = true;

    /**
     * @var string
     */
    private $packageEof = "\r\n\r\n";

    /**
     * @var bool
     */
    private $openEofSplit = false;

    /**
     * @var AbstractPacket
     */
    private $packet;

    /**
     * @param Protocol $protocol
     *
     * @return string
     * @throws RpcException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function encode(Protocol $protocol): string
    {
        $packet = $this->getPacket();
        return $packet->encode($protocol);
    }

    /**
     * @param string $string
     *
     * @return Protocol
     * @throws RpcException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function decode(string $string): Protocol
    {
        $packet = $this->getPacket();
        return $packet->decode($string);
    }

    /**
     * @param mixed    $result
     * @param int|null $code
     * @param string   $message
     * @param null     $data
     *
     * @return string
     * @throws RpcException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function encodeResponse($result, int $code = null, string $message = '', $data = null): string
    {
        $packet = $this->getPacket();
        return $packet->encodeResponse($result, $code, $message, $data);
    }

    /**
     * @param string $string
     *
     * @return Response
     * @throws RpcException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function decodeResponse(string $string): Response
    {
        $packet = $this->getPacket();
        return $packet->decodeResponse($string);
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function defaultPackets(): array
    {
        return [
            self::JSON => bean(JsonPacket::class)
        ];
    }

    /**
     * @return bool
     */
    public function isOpenEofCheck(): bool
    {
        return $this->openEofCheck;
    }

    /**
     * @return string
     */
    public function getPackageEof(): string
    {
        return $this->packageEof;
    }

    /**
     * @return bool
     */
    public function isOpenEofSplit(): bool
    {
        return $this->openEofSplit;
    }

    /**
     * @return PacketInterface
     * @throws RpcException
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function getPacket(): PacketInterface
    {
        if (!empty($this->packet)) {
            return $this->packet;
        }

        $packets = Arr::merge($this->defaultPackets(), $this->packets);
        $packet  = $packets[$this->type] ?? null;
        if (empty($packet)) {
            throw new RpcException(
                sprintf('Packet type(%s) is not supported!', $this->type)
            );
        }

        if (!$packet instanceof AbstractPacket) {
            throw new RpcException(
                sprintf('Packet type(%s) is not instanceof PacketInterface!', $this->type)
            );
        }

        $packet->initialize($this);
        $this->packet = $packet;

        return $packet;
    }
}