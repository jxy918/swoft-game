<?php declare(strict_types=1);


namespace SwoftTest\Rpc\Server\Testing;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Rpc\Exception\RpcException;
use Swoft\Rpc\Packet;
use Swoft\Rpc\Server\Response;
use SwoftTest\Rpc\Server\Testing\Concern\RpcResponseAssertTrait;

/**
 * Class MockResponse
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class MockResponse extends Response
{
    use RpcResponseAssertTrait;

    /**
     * @var \Swoft\Rpc\Response
     */
    protected $returnResponse;

    /**
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     * @throws RpcException
     */
    public function send(): bool
    {
        /* @var Packet $packet */
        $packet = \bean('rpcServerPacket');

        $this->prepare();
        $this->returnResponse = $packet->decodeResponse($this->content);
        return true;
    }
}