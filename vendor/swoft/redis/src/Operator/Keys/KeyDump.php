<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyDump extends Command
{
    /**
     * [Keys] dump
     *
     * @return string
     */
    public function getId()
    {
        return 'dump';
    }
}
