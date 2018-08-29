<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyExists extends Command
{
    /**
     * [Keys] exists
     *
     * @return string
     */
    public function getId()
    {
        return 'exists';
    }
}
