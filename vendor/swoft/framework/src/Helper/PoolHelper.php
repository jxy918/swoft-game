<?php

namespace Swoft\Helper;

/**
 * PoolHelper
 */
class PoolHelper
{
    /**
     * @return string
     */
    public static function getContextCntKey(): string
    {
        return sprintf('connectioins');
    }
}