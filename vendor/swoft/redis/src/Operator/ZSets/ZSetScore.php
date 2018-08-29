<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetScore extends Command
{
    /**
     * [ZSet] zScore
     *
     * @return string
     */
    public function getId()
    {
        return 'zScore';
    }
}
