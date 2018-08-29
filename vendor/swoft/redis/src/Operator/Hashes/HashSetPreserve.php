<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashSetPreserve extends Command
{
    /**
     * [Hash] hSetNx
     *
     * @return string
     */
    public function getId()
    {
        return 'hSetNx';
    }
}
