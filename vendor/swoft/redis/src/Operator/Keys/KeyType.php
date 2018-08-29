<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyType extends Command
{
    /**
     * [Keys] type
     *
     * @return string
     */
    public function getId()
    {
        return 'type';
    }
}
