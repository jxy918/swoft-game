<?php

namespace Swoft\Redis\Operator\PubSubs;

use Swoft\Redis\Operator\Command;

class PubSubPublish extends Command
{
    /**
     * [PubSub] publish
     *
     * @return string
     */
    public function getId()
    {
        return 'publish';
    }
}
