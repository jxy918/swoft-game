<?php declare(strict_types=1);

namespace Swoft\Listener;

use Swoft;
use Swoft\Bean\BeanEvent;
use Swoft\Co;
use Swoft\Context\Context;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Logger;
use Swoft\SwoftEvent;
use function bean;
use function sgo;

/**
 * Class CoroutineCompleteListener
 *
 * @since 2.0
 *
 * @Listener(SwoftEvent::COROUTINE_COMPLETE)
 */
class CoroutineCompleteListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {
        sgo(function () {
            // Wait
            Context::getWaitGroup()->wait();

            /* @var Logger $logger */
            $logger = bean('logger');

            // Add notice log
            if ($logger->isEnable()) {
                $logger->appendNoticeLog();
            }

            // Coroutine destroy
            Swoft::trigger(SwoftEvent::COROUTINE_DESTROY);

            // Destroy request bean
            Swoft::trigger(BeanEvent::DESTROY_REQUEST, $this, Co::tid());

            // Destroy context
            Context::destroy();
        }, false);
    }
}
