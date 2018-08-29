<?php

namespace Swoft\Event\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Core\Coroutine;
use Swoft\Core\RequestContext;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Helper\PoolHelper;
use Swoft\Log\Log;

/**
 * Resource release listener
 *
 * @Listener(AppEvent::RESOURCE_RELEASE)
 */
class ResourceReleaseListener implements EventHandlerInterface
{
    /**
     * @param \Swoft\Event\EventInterface $event
     * @throws \InvalidArgumentException
     */
    public function handle(EventInterface $event)
    {
        // Release system resources
        App::trigger(AppEvent::RESOURCE_RELEASE_BEFORE);

        $connectionKey = PoolHelper::getContextCntKey();
        $connections   = RequestContext::getContextDataByKey($connectionKey, []);
        if (empty($connections)) {
            return;
        }

        /* @var \Swoft\Pool\ConnectionInterface $connection */
        foreach ($connections as $connectionId => $connection) {
            if (!$connection->isRecv()) {
                Log::error(sprintf('%s connection is not received ，forget to getResult()', get_class($connection)));
                $connection->receive();
            }

            Log::error(sprintf('%s connection is not released ，forget to getResult()', get_class($connection)));
            $connection->release(true);
        }
    }
}
