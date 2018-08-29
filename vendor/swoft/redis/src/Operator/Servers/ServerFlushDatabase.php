<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerFlushDatabase extends Command
{
    /**
     * [Server] flushDB
     *
     * @return string
     */
    public function getId()
    {
        return 'flushDB';
    }
}
