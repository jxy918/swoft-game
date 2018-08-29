<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashKeys extends Command
{
    /**
     * [Hash] hKeys
     *
     * @return string
     */
    public function getId()
    {
        return 'hKeys';
    }
}
