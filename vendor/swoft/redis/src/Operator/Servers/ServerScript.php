<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerScript extends Command
{
    /**
     * [Server] script
     *
     * @return string
     */
    public function getId()
    {
        return 'script';
    }
}
