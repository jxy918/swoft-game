<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringGetBit extends Command
{
    /**
     * [String] getBit
     *
     * @return string
     */
    public function getId()
    {
        return 'getBit';
    }
}
