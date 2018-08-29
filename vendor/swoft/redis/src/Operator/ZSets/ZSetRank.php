<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetRank extends Command
{
    /**
     * [ZSet] zRank
     *
     * @return string
     */
    public function getId()
    {
        return 'zRank';
    }
}
