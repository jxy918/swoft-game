<?php declare(strict_types=1);


namespace Swoft\Server\Context;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;
use Swoole\Server as SwooleServer;

/**
 * Class WorkerStopContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class WorkerStopContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var SwooleServer
     */
    private $server;

    /**
     * @var int
     */
    private $workerId;

    /**
     * @param SwooleServer $server
     * @param int          $workerId
     *
     * @return WorkerStopContext
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function new(SwooleServer $server, int $workerId): self
    {
        $self = self::__instance();

        $self->server   = $server;
        $self->workerId = $workerId;

        return $self;
    }

    /**
     * @return SwooleServer
     */
    public function getSwooleServer(): SwooleServer
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getWorkerId(): int
    {
        return $this->workerId;
    }
}