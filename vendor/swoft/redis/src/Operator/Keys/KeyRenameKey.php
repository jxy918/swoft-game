<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyRenameKey extends Command
{
    /**
     * [Keys] rename - renameKey
     *
     * @return string
     */
    public function getId()
    {
        return 'renameKey';
    }
}
