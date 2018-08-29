<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPopLastBlocking extends ListPopFirstBlocking
{
    /**
     * [List] brPop
     *
     * @return string
     */
    public function getId()
    {
        return 'brPop';
    }
}
