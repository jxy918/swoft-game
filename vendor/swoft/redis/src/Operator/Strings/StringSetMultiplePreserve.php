<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringSetMultiplePreserve extends StringSetMultiple
{
    /**
     * [String] mSetNx
     *
     * @return string
     */
    public function getId()
    {
        return 'mSetNx';
    }
}
