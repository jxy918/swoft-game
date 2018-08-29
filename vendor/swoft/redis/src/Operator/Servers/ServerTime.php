<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerTime extends Command
{
    /**
     * [Server] time
     *
     * @return string
     */
    public function getId()
    {
        return 'time';
    }
}
