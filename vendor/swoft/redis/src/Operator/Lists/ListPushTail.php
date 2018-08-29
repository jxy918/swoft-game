<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPushTail extends Command
{
    /**
     * [List] rPush
     *
     * @return string
     */
    public function getId()
    {
        return 'rPush';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        return self::normalizeVariadic($arguments);
    }
}
