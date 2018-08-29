<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerBackgroundSave extends Command
{
    /**
     * [Server] bgSave
     *
     * @return string
     */
    public function getId()
    {
        return 'bgSave';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return $data === 'Background saving started' ? true : $data;
    }
}
