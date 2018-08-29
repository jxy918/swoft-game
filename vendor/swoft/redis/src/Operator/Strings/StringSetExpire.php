<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringSetExpire extends Command
{
    /**
     * [String] setEx
     *
     * @return string
     */
    public function getId()
    {
        return 'setEx';
    }
}
