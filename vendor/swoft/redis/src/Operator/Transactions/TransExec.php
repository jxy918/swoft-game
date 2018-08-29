<?php

namespace Swoft\Redis\Operator\Transactions;

use Swoft\Redis\Operator\Command;

class TransExec extends Command
{
    /**
     * [Transactions] exec
     *
     * @return string
     */
    public function getId()
    {
        return 'exec';
    }
}
