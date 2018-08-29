<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashGet extends Command
{
    /**
     * [Hash] hGet
     *
     * @return string
     */
    public function getId()
    {
        return 'hGet';
    }
}
