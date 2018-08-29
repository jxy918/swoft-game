<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPushHeadX extends Command
{
    /**
     * [List] lPushx
     *
     * @return string
     */
    public function getId()
    {
        return 'lPushx';
    }
}
