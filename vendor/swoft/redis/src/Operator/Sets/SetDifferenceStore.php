<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetDifferenceStore extends SetIntersectionStore
{
    /**
     * [Set] sDiffStore
     *
     * @return string
     */
    public function getId()
    {
        return 'sDiffStore';
    }
}
