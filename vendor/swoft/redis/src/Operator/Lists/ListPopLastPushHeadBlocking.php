<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPopLastPushHeadBlocking extends Command
{
    /**
     * [List] bRPopLPush
     *
     * @return string
     */
    public function getId()
    {
        return 'bRPopLPush';
    }
}
