<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetPop extends Command
{
    /**
     * [Set] sPop
     *
     * @return string
     */
    public function getId()
    {
        return 'sPop';
    }
}
