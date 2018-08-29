<?php

namespace Swoft\Bootstrap\Listeners\Interfaces;

use Swoole\Server;

/**
 *
 *
 * @uses      ConnectionInterface
 * @version   2018年01月10日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface ConnectInterface
{
    public function onConnect(Server $server, int $fd, int $reactorId);
}