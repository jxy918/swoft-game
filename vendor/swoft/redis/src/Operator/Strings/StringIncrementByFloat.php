<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringIncrementByFloat extends Command
{
    /**
     * [String] incrByFloat
     *
     * @return string
     */
    public function getId()
    {
        return 'incrByFloat';
    }
}
