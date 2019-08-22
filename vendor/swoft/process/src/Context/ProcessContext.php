<?php declare(strict_types=1);


namespace Swoft\Process\Context;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\AbstractContext;
use Swoole\Process\Pool;

/**
 * Class ProcessContext
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class ProcessContext extends AbstractContext
{
    use PrototypeTrait;

    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var int
     */
    private $workerId;

    /**
     * @param Pool $pool
     * @param int  $workerId
     *
     * @return ProcessContext
     * @throws ContainerException
     * @throws ReflectionException
     */
    public static function new(Pool $pool, int $workerId): self
    {
        $self = self::__instance();

        $self->pool     = $pool;
        $self->workerId = $workerId;

        return $self;
    }

    /**
     * @return Pool
     */
    public function getPool(): Pool
    {
        return $this->pool;
    }

    /**
     * @return int
     */
    public function getWorkerId(): int
    {
        return $this->workerId;
    }
}