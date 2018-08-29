<?php

namespace Swoft\Redis\Operator\Strings;

use Swoft\Redis\Operator\Command;

class StringGetMultiple extends Command
{
    /**
     * [String] mGet
     *
     * @return string
     */
    public function getId()
    {
        return 'mGet';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
