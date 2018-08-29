<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListInsert extends Command
{
    /**
     * [List] lInsert
     *
     * @return string
     */
    public function getId()
    {
        return 'lInsert';
    }
}
