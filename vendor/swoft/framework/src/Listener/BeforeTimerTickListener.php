<?php declare(strict_types=1);


namespace Swoft\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Context\TimerTickContext;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\SwoftEvent;

/**
 * Class BeforeTimerTickListener
 *
 * @since 2.0
 *
 * @Listener(event=SwoftEvent::TIMER_TICK_BEFORE)
 */
class BeforeTimerTickListener implements EventHandlerInterface
{
    /**
     * Event name
     */
    public const EVENT_NAME = 'timerTick';

    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        [$timerId, $params] = $event->getParams();
        $context = TimerTickContext::new($timerId, $params);

        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => self::EVENT_NAME,
                'uri'         => (string)$timerId,
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }
}