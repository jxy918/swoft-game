<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPushHead extends ListPushTail
{
    /**
     * [List] lPush
     *
     * @return string
     */
    public function getId()
    {
        return 'lPush';
    }
}
