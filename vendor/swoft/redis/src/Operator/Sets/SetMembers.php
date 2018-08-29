<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetMembers extends Command
{
    /**
     * [Set] sMembers
     *
     * @return string
     */
    public function getId()
    {
        return 'sMembers';
    }
}
