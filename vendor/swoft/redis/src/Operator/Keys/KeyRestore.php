<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyRestore extends Command
{
    /**
     * [Keys] restore
     *
     * @return string
     */
    public function getId()
    {
        return 'restore';
    }
}
