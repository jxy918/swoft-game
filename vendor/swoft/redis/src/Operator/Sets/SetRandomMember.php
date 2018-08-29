<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetRandomMember extends Command
{
    /**
     * [Set] sRandMember
     *
     * @return string
     */
    public function getId()
    {
        return 'sRandMember';
    }
}
