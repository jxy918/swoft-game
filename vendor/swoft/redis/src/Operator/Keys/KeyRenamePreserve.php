<?php

namespace Swoft\Redis\Operator\Keys;

use Swoft\Redis\Operator\Command;

class KeyRenamePreserve extends Command
{
    /**
     * [Keys] renameNx
     *
     * @return string
     */
    public function getId()
    {
        return 'renameNx';
    }
}
