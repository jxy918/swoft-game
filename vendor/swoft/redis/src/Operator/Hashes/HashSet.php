<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashSet extends Command
{
    /**
     * [Hash] hSet
     *
     * @return string
     */
    public function getId()
    {
        return 'hSet';
    }
}
