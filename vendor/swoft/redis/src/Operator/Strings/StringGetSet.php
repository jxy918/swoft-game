<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringGetSet extends Command
{
    /**
     * [String] getSet
     *
     * @return string
     */
    public function getId()
    {
        return 'getSet';
    }
}
