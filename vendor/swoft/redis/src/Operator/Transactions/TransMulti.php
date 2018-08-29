<?php

namespace Swoft\Redis\Operator\Transactions;

use Swoft\Redis\Operator\Command;

class TransMulti extends Command
{
    /**
     * [Transactions] multi
     *
     * @return string
     */
    public function getId()
    {
        return 'multi';
    }
}