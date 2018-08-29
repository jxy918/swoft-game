<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetIsMember extends Command
{
    /**
     * [Set] sContains - sIsMember
     *
     * @return string
     */
    public function getId()
    {
        return 'sContains';
    }
}
