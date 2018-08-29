<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetCardinality extends Command
{
    /**
     * [Set] sSize - sCard
     *
     * @return string
     */
    public function getId()
    {
        return 'sSize';
    }
}
