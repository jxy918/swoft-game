<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyRandom extends Command
{
    /**
     * [Keys] randomKey
     *
     * @return string
     */
    public function getId()
    {
        return 'randomKey';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return $data !== '' ? $data : null;
    }
}
