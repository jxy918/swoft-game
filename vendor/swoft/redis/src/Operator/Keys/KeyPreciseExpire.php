<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyPreciseExpire extends Command
{
    /**
     * [Keys] pexipre
     *
     * @return string
     */
    public function getId()
    {
        return 'pexipre';
    }
}
