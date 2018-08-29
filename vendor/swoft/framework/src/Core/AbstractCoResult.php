<?php

namespace Swoft\Core;

use Swoft\Pool\ConnectionInterface;

/**
 * The result of cor
 */
abstract class AbstractCoResult implements ResultInterface
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
     * AbstractCorResult constructor.
     *
     * @param mixed  $connection
     * @param string $profileKey
     */
    public function __construct($connection = null, string $profileKey = '')
    {
        $this->connection = $connection;
        $this->profileKey = $profileKey;
    }

    /**
     * Receive by defer
     *
     * @param bool $defer
     *
     * @return mixed
     */
    public function recv($defer = false)
    {
        if ($this->connection instanceof ConnectionInterface) {
            $result = $this->connection->receive();
            $this->connection->release();

            return $result;
        }

        $result = $this->connection->recv();
        if ($defer) {
            $this->connection->setDefer(false);
        }

        return $result;
    }
}