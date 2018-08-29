<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetRemove extends Command
{
    /**
     * [Set] sRemove - sRem
     *
     * @return string
     */
    public function getId()
    {
        return 'sRemove';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        return self::normalizeVariadic($arguments);
    }
}
