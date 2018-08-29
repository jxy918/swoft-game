<?php

namespace Swoft\Redis\Operator\Transactions;

use Swoft\Redis\Operator\Command;

class TransUnWatch extends Command
{
    /**
     * [Transactions] unWatch
     *
     * @return string
     */
    public function getId()
    {
        return 'unWatch';
    }
}