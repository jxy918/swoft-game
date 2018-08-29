<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetUnion extends SetIntersection
{
    /**
     * [Set] sUnion
     *
     * @return string
     */
    public function getId()
    {
        return 'sUnion';
    }
}
