<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetDifference extends SetIntersection
{
    /**
     * [Set] sDiff
     *
     * @return string
     */
    public function getId()
    {
        return 'sDiff';
    }
}
