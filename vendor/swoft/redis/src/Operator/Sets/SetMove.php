<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetMove extends Command
{
    /**
     * [Set] sMove
     *
     * @return string
     */
    public function getId()
    {
        return 'sMove';
    }
}
