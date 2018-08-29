<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetReverseRange extends ZSetRange
{
    /**
     * [ZSet] zRevRange
     *
     * @return string
     */
    public function getId()
    {
        return 'zRevRange';
    }
}
