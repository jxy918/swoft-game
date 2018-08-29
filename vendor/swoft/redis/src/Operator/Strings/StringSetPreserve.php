<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringSetPreserve extends Command
{
    /**
     * [String] setNx
     *
     * @return string
     */
    public function getId()
    {
        return 'setNx';
    }
}
