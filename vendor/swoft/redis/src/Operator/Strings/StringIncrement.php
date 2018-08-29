<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringIncrement extends Command
{
    /**
     * [String] incr
     *
     * @return string
     */
    public function getId()
    {
        return 'incr';
    }
}
