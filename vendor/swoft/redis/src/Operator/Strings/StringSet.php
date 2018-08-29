<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringSet extends Command
{
    /**
     * [String] set
     *
     * @return string
     */
    public function getId()
    {
        return 'set';
    }
}
