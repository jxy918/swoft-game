<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyGetKeys extends Command
{
    /**
     * [Keys] getKeys - Keys
     *
     * @return string
     */
    public function getId()
    {
        return 'getKeys';
    }
}
