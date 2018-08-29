<?php

namespace Swoft\Core;

use Swoft\Pool\ConnectionInterface;

/**
 * AbstractResult
 */
abstract class AbstractResult implements ResultInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var string
     */
    protected $profileKey;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * AbstractCorResult constructor.
     *
     * @param mixed  $result
     * @param mixed  $connection
     * @param string $profileKey
     */
    public function __construct($result, $connection = null, string $profileKey = '')
    {
        $this->result     = $result;
        $this->connection = $connection;
        $this->profileKey = $profileKey;
    }

    /**
     * Receive by defer
     *
     * @param bool $defer
     * @param bool $release
     *
     * @return mixed
     */
    protected function recv(bool $defer = false, bool $release = true)
    {
        if ($this->connection instanceof ConnectionInterface) {
            $result = $this->connection->receive();
            $this->release($release);

            return $result;
        }

        $result = $this->connection->recv();
        if ($defer) {
            $this->connection->setDefer(false);
        }

        return $result;
    }

    /**
     * @param bool $release
     */
    protected function release(bool $release = true)
    {
        if ($this->connection instanceof ConnectionInterface && $release) {
            $this->connection->release();
        }
    }
}