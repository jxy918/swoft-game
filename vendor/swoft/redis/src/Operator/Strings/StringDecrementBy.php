<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringDecrementBy extends Command
{
    /**
     * [String] decrBy
     *
     * @return string
     */
    public function getId()
    {
        return 'decrBy';
    }
}
