<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringSetRange extends Command
{
    /**
     * [String] setRange
     *
     * @return string
     */
    public function getId()
    {
        return 'setRange';
    }
}
