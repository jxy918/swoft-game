<?php declare(strict_types=1);


namespace Swoft\Rpc\Server;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;
use Swoole\Server;

/**
 * Class ServiceConnectContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class ServiceConnectContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var int
     */
    protected $fd;

    /**
     * @var int
     */
    protected $reactorId;

    /**
     * @param Server $server
     * @param int    $fd
     * @param int    $reactorId
     *
     * @return ServiceConnectContext
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(Server $server, int $fd, int $reactorId): self
    {
        $instance = self::__instance();

        $instance->server    = $server;
        $instance->fd        = $fd;
        $instance->reactorId = $reactorId;

        return $instance;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return int
     */
    public function getReactorId(): int
    {
        return $this->reactorId;
    }
}