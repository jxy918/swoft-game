<?php declare(strict_types=1);

namespace Swoft\Event;

/**
 * Class EventHandlerInterface
 *
 * @since 2.0
 */
interface EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void;
}
