<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListLength extends Command
{
    /**
     * [List] lLen
     *
     * @return string
     */
    public function getId()
    {
        return 'lLen';
    }
}
