<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPopFirst extends Command
{
    /**
     * [List] lPop
     *
     * @return string
     */
    public function getId()
    {
        return 'lPop';
    }
}
