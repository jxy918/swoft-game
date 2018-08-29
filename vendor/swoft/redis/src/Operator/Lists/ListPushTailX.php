<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPushTailX extends Command
{
    /**
     * [List] rPushx
     *
     * @return string
     */
    public function getId()
    {
        return 'rPushx';
    }
}
