<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringDecrement extends Command
{
    /**
     * [String] decr
     *
     * @return string
     */
    public function getId()
    {
        return 'decr';
    }
}
