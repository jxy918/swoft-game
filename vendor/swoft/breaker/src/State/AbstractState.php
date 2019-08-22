<?php declare(strict_types=1);


namespace Swoft\Breaker\State;


use ReflectionException;
use Swoft\Bean\Concern\PrototypeTrait;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Breaker\Breaker;
use Swoft\Breaker\Contract\StateInterface;

/**
 * Class AbstractState
 *
 * @since 2.0
 */
abstract class AbstractState implements StateInterface
{
    use PrototypeTrait;

    /**
     * @var Breaker
     */
    protected $breaker;

    /**
     * @param Breaker $breaker
     *
     * @return AbstractState
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function new(Breaker $breaker): self
    {
        $self = self::__instance();

        $self->breaker = $breaker;

        // Reset state
        $self->reset();
        return $self;
    }

    /**
     * Check status
     */
    public function check(): void
    {

    }

    /**
     * Reset
     */
    public function reset(): void
    {
        return;
    }

    /**
     * Success
     */
    public function success(): void
    {
        $this->breaker->incSucCount();

        // Reset failCount
        $this->breaker->resetFailCount();
    }

    /**
     * Exception
     */
    public function exception(): void
    {
        $this->breaker->incFailCount();

        // Reset sucCount
        $this->breaker->resetSucCount();
    }
}