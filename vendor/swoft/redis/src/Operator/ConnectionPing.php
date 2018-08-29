<?php

namespace Swoft\Redis\Operator;

class ConnectionPing extends Command
{
    /**
     * [Connect] ping
     *
     * @return string
     */
    public function getId()
    {
        return 'ping';
    }
}
