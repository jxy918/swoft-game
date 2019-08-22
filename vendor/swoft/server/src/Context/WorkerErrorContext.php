<?php declare(strict_types=1);


namespace Swoft\Server\Context;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;
use Swoole\Server as SwooleServer;

/**
 * Class WorkerErrorContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class WorkerErrorContext extends AbstractContext
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
     * @var int
     */
    private $workerPid;

    /**
     * @var int
     */
    private $exitCode;

    /**
     * @var int
     */
    private $sigal;

    /**
     * @param SwooleServer $server
     * @param int          $workerId
     *
     * @param int          $workerPid
     * @param int          $exitCode
     * @param int          $signal
     *
     * @return WorkerErrorContext
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function new(SwooleServer $server, int $workerId, int $workerPid, int $exitCode, int $signal): self
    {
        $self = self::__instance();

        $self->server    = $server;
        $self->workerId  = $workerId;
        $self->workerPid = $workerPid;
        $self->exitCode  = $exitCode;
        $self->sigal     = $signal;

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

    /**
     * @return int
     */
    public function getWorkerPid(): int
    {
        return $this->workerPid;
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * @return int
     */
    public function getSigal(): int
    {
        return $this->sigal;
    }
}