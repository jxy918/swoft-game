<?php

namespace Swoft\Redis\Operator\Sets;

use Swoft\Redis\Operator\Command;

class SetIntersectionStore extends Command
{
    /**
     * [Set] sInterStore
     *
     * @return string
     */
    public function getId()
    {
        return 'sInterStore';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[1])) {
            return array_merge(array($arguments[0]), $arguments[1]);
        }

        return $arguments;
    }
}
