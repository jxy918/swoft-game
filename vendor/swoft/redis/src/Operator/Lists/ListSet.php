<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListSet extends Command
{
    /**
     * [List] lSet
     *
     * @return string
     */
    public function getId()
    {
        return 'lSet';
    }
}
