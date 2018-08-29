<?php

namespace Swoft\Redis\Operator\ZSets;

use Swoft\Redis\Operator\Command;

class ZSetRemove extends Command
{
    /**
     * [ZSet] zDelete - zRem
     *
     * @return string
     */
    public function getId()
    {
        return 'zDelete';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        return self::normalizeVariadic($arguments);
    }
}
