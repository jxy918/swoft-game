<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetCount extends Command
{
    /**
     * [ZSet] zCount
     *
     * @return string
     */
    public function getId()
    {
        return 'zCount';
    }
}
