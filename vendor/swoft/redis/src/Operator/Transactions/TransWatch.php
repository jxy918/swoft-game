<?php

namespace Swoft\Redis\Operator\Transactions;

use Swoft\Redis\Operator\Command;

class TransWatch extends Command
{
    /**
     * [Transactions] watch
     *
     * @return string
     */
    public function getId()
    {
        return 'watch';
    }
}