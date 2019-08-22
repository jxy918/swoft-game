<?php declare(strict_types=1);

namespace Swoft\Tcp\Server;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Server\Exception\ServerException;
use Swoft\Server\Server;
use Swoole\Server as SwServer;

/**
 * Class TcpServer
 *
 * @since 2.0
 * @Bean("tcpServer")
 */
class TcpServer extends Server
{
    // protected static $serverType = 'TCP';

    /**
     * Default listen port
     *
     * @var int
     */
    protected $port = 18309;

    /**
     * @var string
     */
    protected $pidName = 'swoft-tcp';

    /**
     * @var string
     */
    protected $pidFile = '@runtime/swoft-tcp.pid';

    /**
     * @var string
     */
    protected $commandFile = '@runtime/swoft-tcp.command';

    /**
     * Start server
     *
     * @throws ContainerException
     * @throws ServerException
     */
    public function start(): void
    {
        $this->swooleServer = new SwServer($this->host, $this->port, $this->mode, $this->type);

        // Start server
        $this->startSwoole();
    }
}
