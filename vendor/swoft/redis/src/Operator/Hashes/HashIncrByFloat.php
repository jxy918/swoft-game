<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashIncrByFloat extends Command
{
    /**
     * [Hash] hIncrByFloat
     *
     * @return string
     */
    public function getId()
    {
        return 'hIncrByFloat';
    }
}
