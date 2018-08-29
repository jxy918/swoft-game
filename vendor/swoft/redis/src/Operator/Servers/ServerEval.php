<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerEval extends Command
{
    /**
     * [Server] eval
     *
     * @return string
     */
    public function getId()
    {
        return 'eval';
    }

    /**
     * Calculates the SHA1 hash of the body of the script.
     *
     * @return string SHA1 hash.
     */
    public function getScriptHash()
    {
        return sha1($this->getArgument(0));
    }
}
