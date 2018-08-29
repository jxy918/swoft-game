<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetIntersection extends Command
{
    /**
     * [Set] sInter
     *
     * @return string
     */
    public function getId()
    {
        return 'sInter';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
