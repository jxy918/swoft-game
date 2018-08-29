<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetReverseRank extends Command
{
    /**
     * [ZSet] zRevRank
     *
     * @return string
     */
    public function getId()
    {
        return 'zRevRank';
    }
}
