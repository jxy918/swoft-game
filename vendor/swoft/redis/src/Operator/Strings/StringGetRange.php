<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringGetRange extends Command
{
    /**
     * [String] getRange
     *
     * @return string
     */
    public function getId()
    {
        return 'getRange';
    }
}
