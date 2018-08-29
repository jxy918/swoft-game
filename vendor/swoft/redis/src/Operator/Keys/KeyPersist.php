<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyPersist extends Command
{
    /**
     * [Keys] persist
     *
     * @return string
     */
    public function getId()
    {
        return 'persist';
    }
}
