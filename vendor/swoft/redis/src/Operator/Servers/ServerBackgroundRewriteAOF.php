<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerBackgroundRewriteAOF extends Command
{
    /**
     * [Server] bgrewriteaof
     *
     * @return string
     */
    public function getId()
    {
        return 'bgrewriteaof';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return $data == 'Background append only file rewriting started';
    }
}
