<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringAppend extends Command
{
    /**
     * [String] append
     *
     * @return string
     */
    public function getId()
    {
        return 'append';
    }
}
