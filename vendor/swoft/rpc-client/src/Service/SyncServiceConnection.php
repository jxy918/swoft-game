<?php

namespace Swoft\Rpc\Client\Service;

use Swoft\Rpc\Server\Exception\RpcServerException;

/**
 * Sync service connection
 */
class SyncServiceConnection extends AbstractServiceConnection
{
    /**
     * @var resource
     */
    protected $connection;

    /**
     * Create connection
     *
     * @throws RpcServerException
     */
    public function createConnection()
    {
        $address = $this->pool->getConnectionAddress();
        $timeout = $this->pool->getTimeout();
        list($host, $port) = explode(":", $address);

        $remoteSocket = sprintf('tcp://%s:%d', $host, $port);
        $fp           = stream_socket_client($remoteSocket, $errno, $errstr, $timeout);
        if (!$fp) {
            throw new RpcServerException(sprintf('stream_socket_client connect error errno=%s msg=%s', $errno, $errstr));
        }
        $this->connection = $fp;
    }

    /**
     * Reconnect
     */
    public function reconnect()
    {
        $this->createConnection();
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        $result = @stream_socket_get_name($this->connection, true);
        if ($result === false) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function receive()
    {
        $result = $this->recv();
        $this->recv = true;
        return $result;
    }

    /**
     * @param string $data
     *
     * @return bool
     */
    public function send(string $data): bool
    {
        $result = fwrite($this->connection, $data);
        $this->recv = false;
        return $result;
    }

    /**
     * @return string
     */
    public function recv(): string
    {
        return fread($this->connection, 1024);
    }
}
