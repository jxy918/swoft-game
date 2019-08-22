<?php declare(strict_types=1);

namespace Swoft\Server\Event;

use Swoft\Event\Event;
use Swoole\Server;

/**
 * Class ServerStartEvent
 *
 * @since 2.0
 */
class ServerStartEvent extends Event
{
    /**
     * @var Server;
     */
    public $coServer;

    /**
     * Class constructor.
     *
     * @param string $name
     * @param Server $server
     */
    public function __construct(string $name, Server $server)
    {
        parent::__construct($name);

        $this->coServer = $server;
    }
}
