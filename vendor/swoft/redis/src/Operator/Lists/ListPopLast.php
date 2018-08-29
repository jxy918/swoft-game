<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPopLast extends Command
{
    /**
     * [List] rPop
     *
     * @return string
     */
    public function getId()
    {
        return 'rPop';
    }
}
