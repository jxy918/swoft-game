<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetRemoveRangeByScore extends Command
{
    /**
     * [ZSet] zDeleteRangeByScore - zRemRangeByScore
     *
     * @return string
     */
    public function getId()
    {
        return 'zDeleteRangeByScore';
    }
}
