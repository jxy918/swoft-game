<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetReverseRangeByLex extends ZSetRangeByLex
{
    /**
     * [ZSet] zRevRangeByLex
     *
     * @return string
     */
    public function getId()
    {
        return 'zRevRangeByLex';
    }
}
