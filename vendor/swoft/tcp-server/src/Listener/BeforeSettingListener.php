<?php declare(strict_types=1);

namespace Swoft\Tcp\Server\Listener;

use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use Swoft\Server\ServerEvent;
use Swoft\Tcp\Protocol;
use Swoft\Tcp\Server\TcpServer;

/**
 * Class BeforeSettingListener
 *
 * @Listener(ServerEvent::BEFORE_SETTING)
 */
class BeforeSettingListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        /** @var TcpServer $server */
        $server = $event->getTarget();

        if ($server instanceof TcpServer) {
            CLog::debug('sync tcp protocol setting from bean(tcpServerProtocol)');

            /** @var Protocol $proto */
            $proto = bean('tcpServerProtocol');
            $server->setSetting($proto->getConfig());
        }
    }
}
