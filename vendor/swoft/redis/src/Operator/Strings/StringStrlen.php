<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringStrlen extends Command
{
    /**
     * [String] strLen
     *
     * @return string
     */
    public function getId()
    {
        return 'strLen';
    }
}
