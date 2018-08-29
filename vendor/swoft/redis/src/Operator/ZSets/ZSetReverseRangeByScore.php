<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetReverseRangeByScore extends ZSetRangeByScore
{
    /**
     * [ZSet] zRevRangeByScore
     *
     * @return string
     */
    public function getId()
    {
        return 'zRevRangeByScore';
    }
}
