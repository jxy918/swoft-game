<?php declare(strict_types=1);


namespace Swoft\Breaker\State;

use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class CloseState
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::PROTOTYPE)
 */
class CloseState extends AbstractState
{
    /**
     * Reset state
     */
    public function reset(): void
    {
        $this->breaker->resetFailCount();
    }

    /**
     * Success
     */
    public function success(): void
    {
        // Reset failCount
        $this->breaker->resetFailCount();
        return;
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function exception(): void
    {
        parent::exception();

        if ($this->breaker->isReachFailThreshold()) {
            $this->breaker->moveToOpen();
        }
    }
}