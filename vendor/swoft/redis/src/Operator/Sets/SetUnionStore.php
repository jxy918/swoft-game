<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetUnionStore extends SetIntersectionStore
{
    /**
     * [Set] sUnionStore
     *
     * @return string
     */
    public function getId()
    {
        return 'sUnionStore';
    }
}
