<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListIndex extends Command
{
    /**
     * [List] lIndex - lGet
     *
     * @return string
     */
    public function getId()
    {
        return 'lGet';
    }
}
