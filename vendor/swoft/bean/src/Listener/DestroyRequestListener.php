<?php declare(strict_types=1);

namespace Swoft\Bean\Listener;

use Swoft\Bean\BeanFactory;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Bean\BeanEvent;

/**
 * Class DestroyRequestListener
 *
 * @since 2.0
 *
 * @Listener(BeanEvent::DESTROY_REQUEST)
 */
class DestroyRequestListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {
        $id = (string)$event->getParam(0, '');
        if (empty($id)) {
            return;
        }

        BeanFactory::destroyRequest($id);
    }
}