<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashIncrementBy extends Command
{
    /**
     * [Hash] hIncrBy
     *
     * @return string
     */
    public function getId()
    {
        return 'hIncrBy';
    }
}
