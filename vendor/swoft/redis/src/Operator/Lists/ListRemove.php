<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListRemove extends Command
{
    /**
     * [List] lRem
     *
     * @return string
     */
    public function getId()
    {
        return 'lRem';
    }
}
