<?php

namespace Swoft\Redis\Operator\PubSubs;

use Swoft\Redis\Operator\Command;

class PubSubPSubscribe extends Command
{
    /**
     * [PubSub] pSubscribe
     *
     * @return string
     */
    public function getId()
    {
        return 'pSubscribe';
    }
}
