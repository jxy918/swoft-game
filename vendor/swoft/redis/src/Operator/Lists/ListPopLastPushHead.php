<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPopLastPushHead extends Command
{
    /**
     * [List] rpoplpush
     *
     * @return string
     */
    public function getId()
    {
        return 'rpoplpush';
    }
}
