<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetCardinality extends Command
{
    /**
     * [ZSet] zCard
     *
     * @return string
     */
    public function getId()
    {
        return 'zCard';
    }
}
