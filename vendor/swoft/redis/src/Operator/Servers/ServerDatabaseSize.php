<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerDatabaseSize extends Command
{
    /**
     * [Server] dbSize
     *
     * @return string
     */
    public function getId()
    {
        return 'dbSize';
    }
}
