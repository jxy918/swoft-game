<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListTrim extends Command
{
    /**
     * [List] listTrim - lTrim
     *
     * @return string
     */
    public function getId()
    {
        return 'listTrim';
    }
}
