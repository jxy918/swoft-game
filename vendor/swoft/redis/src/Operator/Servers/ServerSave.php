<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerSave extends Command
{
    /**
     * [Server] save
     *
     * @return string
     */
    public function getId()
    {
        return 'save';
    }
}
