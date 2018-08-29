<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashLength extends Command
{
    /**
     * [Hash] hLen
     *
     * @return string
     */
    public function getId()
    {
        return 'hLen';
    }
}
