<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetAdd extends Command
{
    /**
     * [Set] sAdd
     *
     * @return string
     */
    public function getId()
    {
        return 'sAdd';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        return self::normalizeVariadic($arguments);
    }
}
