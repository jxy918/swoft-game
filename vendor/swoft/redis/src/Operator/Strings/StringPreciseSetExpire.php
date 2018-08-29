<?php

namespace Swoft\Redis\Operator\Strings;

class StringPreciseSetExpire extends StringSetExpire
{
    /**
     * [String] psetEx
     *
     * @return string
     */
    public function getId()
    {
        return 'psetEx';
    }
}
