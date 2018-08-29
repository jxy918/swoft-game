<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyDelete extends Command
{
    /**
     * [Keys] del
     *
     * @return string
     */
    public function getId()
    {
        return 'del';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        return self::normalizeArguments($arguments);
    }
}
