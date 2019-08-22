<?php declare(strict_types=1);

namespace Swoft\Listener;

use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Session\Session;
use Swoft\SwoftEvent;

/**
 * Class SessionCompleteListener
 * @Listener(SwoftEvent::SESSION_COMPLETE)
 */
class SessionCompleteListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {
        // TODO: Implement handle() method.

        Session::destroy($event->getTarget());
    }
}
