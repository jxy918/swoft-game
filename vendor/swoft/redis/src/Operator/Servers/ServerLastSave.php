<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerLastSave extends Command
{
    /**
     * [Server] lastSave
     *
     * @return string
     */
    public function getId()
    {
        return 'lastSave';
    }
}
