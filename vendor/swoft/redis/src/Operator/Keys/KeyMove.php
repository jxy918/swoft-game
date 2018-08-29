<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyMove extends Command
{
    /**
     * [Keys] move
     *
     * @return string
     */
    public function getId()
    {
        return 'move';
    }
}
