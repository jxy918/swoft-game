<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyPreciseTimeToLive extends KeyTimeToLive
{
    /**
     * [Keys] pttl
     *
     * @return string
     */
    public function getId()
    {
        return 'pttl';
    }
}
