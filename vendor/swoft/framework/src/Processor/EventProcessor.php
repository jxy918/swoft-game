<?php declare(strict_types=1);

namespace Swoft\Processor;

use Swoft;
use Swoft\Event\ListenerRegister;
use Swoft\Event\Manager\EventManager;
use Swoft\Log\Helper\CLog;
use Swoft\SwoftEvent;
use function bean;

/**
 * Event processor
 * @since 2.0
 */
class EventProcessor extends Processor
{
    /**
     * Handle event register
     * @return bool
     */
    public function handle(): bool
    {
        if (!$this->application->beforeEvent()) {
            CLog::warning('Stop event processor by beforeEvent return false');
            return false;
        }

        /** @var EventManager $eventManager */
        $eventManager = bean('eventManager');
        [$count1, $count2] = ListenerRegister::register($eventManager);

        CLog::info('Event manager initialized(%d listener, %d subscriber)', $count1, $count2);

        // Trigger a app init event
        Swoft::trigger(SwoftEvent::APP_INIT_COMPLETE);

        return $this->application->afterEvent();
    }
}
