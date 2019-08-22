<?php declare(strict_types=1);


namespace Swoft\Listener;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Context\Context;
use Swoft\Context\TimerAfterContext;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\Log;
use Swoft\SwoftEvent;

/**
 * Class BeforeTimerAfterListener
 *
 * @since 2.0
 *
 * @Listener(event=SwoftEvent::TIMER_AFTER_BEFORE)
 */
class BeforeTimerAfterListener implements EventHandlerInterface
{
    /**
     * Event name
     */
    public const EVENT_NAME = 'timerAfter';

    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        $context = TimerAfterContext::new(1, []);

        if (Log::getLogger()->isEnable()) {
            $data = [
                'event'       => self::EVENT_NAME,
                'uri'         => (string)1,
                'requestTime' => microtime(true),
            ];
            $context->setMulti($data);
        }

        Context::set($context);
    }
}