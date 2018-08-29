<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetIncrementBy extends Command
{
    /**
     * [ZSet] zIncrBy
     *
     * @return string
     */
    public function getId()
    {
        return 'zIncrBy';
    }
}
