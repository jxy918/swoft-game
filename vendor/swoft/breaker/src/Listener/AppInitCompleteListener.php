<?php declare(strict_types=1);


namespace Swoft\Breaker\Listener;


use ReflectionException;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Breaker\BreakerManager;
use Swoft\Breaker\BreakerRegister;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\SwoftEvent;

/**
 * Class AppInitCompleteListener
 *
 * @since 2.0
 *
 * @Listener(event=SwoftEvent::APP_INIT_COMPLETE)
 */
class AppInitCompleteListener implements EventHandlerInterface
{
    /**
     * @Inject()
     *
     * @var BreakerManager
     */
    private $breakerManger;

    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        $breakers = BreakerRegister::getBreakers();
        $this->breakerManger->initBreaker($breakers);
    }
}