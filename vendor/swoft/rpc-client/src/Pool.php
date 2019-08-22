<?php declare(strict_types=1);


namespace Swoft\Rpc\Client;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Connection\Pool\AbstractPool;
use Swoft\Connection\Pool\Contract\ConnectionInterface;
use Swoft\Rpc\Client\Exception\RpcClientException;

/**
 * Class Pool
 *
 * @since 2.0
 */
class Pool extends AbstractPool
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @return ConnectionInterface
     * @throws Exception\RpcClientException
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function createConnection(): ConnectionInterface
    {
        if (empty($this->client)) {
            throw new RpcClientException(
                sprintf('Pool(%s) client can not be null!', __CLASS__)
            );
        }

        return $this->client->createConnection($this);
    }
}