<?php

namespace Swoft\Redis\Operator\Servers;

use Swoft\Redis\Operator\Command;

class ServerEvalSHA extends ServerEval
{
    /**
     * [Server] evalSha
     *
     * @return string
     */
    public function getId()
    {
        return 'evalSha';
    }

    /**
     * Returns the SHA1 hash of the body of the script.
     *
     * @return string SHA1 hash.
     */
    public function getScriptHash()
    {
        return $this->getArgument(0);
    }
}
