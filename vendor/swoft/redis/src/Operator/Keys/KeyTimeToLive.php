<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyTimeToLive extends Command
{
    /**
     * [Keys] ttl
     *
     * @return string
     */
    public function getId()
    {
        return 'ttl';
    }
}
