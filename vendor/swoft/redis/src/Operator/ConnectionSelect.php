<?php

namespace Swoft\Redis\Operator;

class ConnectionSelect extends Command
{
    /**
     * [Connect] select
     *
     * @return string
     */
    public function getId()
    {
        return 'select';
    }
}