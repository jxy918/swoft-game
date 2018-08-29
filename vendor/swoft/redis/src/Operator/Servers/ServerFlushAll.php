<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerFlushAll extends Command
{
    /**
     * [Server] flushAll
     *
     * @return string
     */
    public function getId()
    {
        return 'flushAll';
    }
}
