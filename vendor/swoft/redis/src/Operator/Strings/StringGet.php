<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringGet extends Command
{
    /**
     * [String] append
     *
     * @return string
     */
    public function getId()
    {
        return 'get';
    }
}
