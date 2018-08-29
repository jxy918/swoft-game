<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyPreciseExpireAt extends KeyExpireAt
{
    /**
     * [Keys] pexpireAt
     *
     * @return string
     */
    public function getId()
    {
        return 'pexpireAt';
    }
}
