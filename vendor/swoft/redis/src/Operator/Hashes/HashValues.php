<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashValues extends Command
{
    /**
     * [Hash] hVals
     *
     * @return string
     */
    public function getId()
    {
        return 'hVals';
    }
}
