<?php declare(strict_types=1);


namespace Swoft\Breaker\State;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Breaker\Exception\BreakerException;
use Swoole\Coroutine\Channel;

/**
 * Class HalfOpenState
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class HalfOpenState extends AbstractState
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * Check status
     * @throws BreakerException
     */
    public function check(): void
    {
        if ($this->channel->isEmpty()) {
            throw new BreakerException(sprintf('Out of half open limit!(%s)', $this->breaker->getSucThreshold()));
        }

        $this->channel->pop();
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function success(): void
    {
        parent::success();
        if ($this->breaker->isReachSucCount()) {
            $this->breaker->moveToClose();
        }
    }

    /**
     * Reset
     */
    public function reset(): void
    {
        $times         = $this->breaker->getSucThreshold();
        $this->channel = new Channel($times);

        while ($times > 0) {
            $this->channel->push($times);
            $times--;
        }

        $this->breaker->resetSucCount();
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function exception(): void
    {
        parent::exception();
        $this->breaker->moveToOpen();
    }
}