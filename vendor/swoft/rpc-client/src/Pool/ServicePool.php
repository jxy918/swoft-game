<?php

namespace Swoft\Rpc\Client\Pool;

use Swoft\App;
use Swoft\Pool\ConnectionInterface;
use Swoft\Pool\ConnectionPool;
use Swoft\Rpc\Client\Service\ServiceConnection;
use Swoft\Rpc\Client\Service\SyncServiceConnection;

/**
 * Service pool
 */
class ServicePool extends ConnectionPool
{
    /**
     * Create connection
     *
     * @return ConnectionInterface
     */
    public function createConnection(): ConnectionInterface
    {
        if (App::isCoContext()) {
            return new ServiceConnection($this);
        }

        return new SyncServiceConnection($this);
    }
}
