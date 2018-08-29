<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringSetBit extends Command
{
    /**
     * [String] setBit
     *
     * @return string
     */
    public function getId()
    {
        return 'setBit';
    }
}
