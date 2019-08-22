<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Listener;

use Swoft\Event\Annotation\Mapping\Subscriber;
use Swoft\Event\EventInterface;
use Swoft\Event\EventSubscriberInterface;
use Swoft\Log\Helper\CLog;
use Swoft\Server\SwooleEvent;
use Swoft\Session\Session;
use Swoft\WebSocket\Server\WebSocketServer;

/**
 * Class WorkerEndSubscriber
 *
 * @since 2.0.5
 * @Subscriber()
 */
class WorkerEndSubscriber implements EventSubscriberInterface
{
    /**
     * Configure events and corresponding processing methods (you can configure the priority)
     *
     * @return array
     * [
     *  'event name' => 'handler method'
     *  'event name' => ['handler method', priority]
     * ]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SwooleEvent::WORKER_STOP => 'onWorkerStop',
        ];
    }

    /**
     * @param EventInterface $event
     */
    public function onWorkerStop(EventInterface $event): void
    {
        /** @var WebSocketServer $server */
        $server = $event->getTarget();

        // Close all connection
        if ($server instanceof WebSocketServer) {
            $count = 0;

            foreach (Session::getSessions() as $sid => $sess) {
                $ok = $server->disconnect((int)$sid, 0, 'closed by server');

                if ($ok === true) {
                    $count++;
                }
            }

            CLog::info('Close %d ws connection on worker stop', $count);
        }

        Session::clear();
    }
}
