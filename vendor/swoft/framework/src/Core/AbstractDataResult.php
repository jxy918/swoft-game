<?php

namespace Swoft\Core;

use Swoft\Pool\ConnectionInterface;

/**
 * Sync result
 */
abstract class AbstractDataResult implements ResultInterface
{
    /**
     * @var mixed
     */
    protected $connection;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * AbstractDataResult constructor.
     *
     * @param mixed $data
     * @param mixed $connection
     */
    public function __construct($data, $connection = null)
    {
        $this->data       = $data;
        $this->connection = $connection;
    }

    /**
     * @return void
     */
    protected function release()
    {
        if ($this->connection instanceof ConnectionInterface) {
            $this->connection->release();
        }

        return;
    }

}