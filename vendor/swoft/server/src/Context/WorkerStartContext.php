<?php declare(strict_types=1);


namespace Swoft\Server\Context;


use ReflectionException;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;
use Swoole\Server as SwooleServer;
use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class WorkerStartContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class WorkerStartContext extends AbstractContext
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
     * @return WorkerStartContext
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