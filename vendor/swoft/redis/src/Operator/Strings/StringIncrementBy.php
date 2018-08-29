<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringIncrementBy extends Command
{
    /**
     * [String] incrBy
     *
     * @return string
     */
    public function getId()
    {
        return 'incrBy';
    }
}
