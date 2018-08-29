<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashSetMultiple extends Command
{
    /**
     * [Hash] hMSet
     *
     * @return string
     */
    public function getId()
    {
        return 'hMSet';
    }
}
