<?php

namespace Swoft\Redis\Operator\Lists;

use Swoft\Redis\Operator\Command;

class ListPopFirstBlocking extends Command
{
    /**
     * [List] blPop
     *
     * @return string
     */
    public function getId()
    {
        return 'blPop';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[0])) {
            list($arguments, $timeout) = $arguments;
            array_push($arguments, $timeout);
        }

        return $arguments;
    }
}
