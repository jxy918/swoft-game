<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetRemoveRangeByRank extends Command
{
    /**
     * [ZSet] zDeleteRangeByRank - zRemRangeByRank
     *
     * @return string
     */
    public function getId()
    {
        return 'zDeleteRangeByRank';
    }
}
