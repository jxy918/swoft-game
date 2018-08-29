<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListRange extends Command
{
    /**
     * [List] lGetRange - lRange
     *
     * @return string
     */
    public function getId()
    {
        return 'lGetRange';
    }
}
