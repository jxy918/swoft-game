<?php

namespace Swoft\Redis\Operator\Hashes;

use Swoft\Redis\Operator\Command;

class HashDelete extends Command
{
    /**
     * [Hash] hDel
     *
     * @return string
     */
    public function getId()
    {
        return 'hDel';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        return self::normalizeVariadic($arguments);
    }
}
