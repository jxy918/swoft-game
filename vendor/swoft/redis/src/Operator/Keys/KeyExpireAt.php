<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyExpireAt extends Command
{
    /**
     * [Keys] expireAt
     *
     * @return string
     */
    public function getId()
    {
        return 'expireAt';
    }
}
