<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringBitCount extends Command
{
    /**
     * [String] bitCount
     *
     * @return string
     */
    public function getId()
    {
        return 'bitCount';
    }
}
