<?php declare(strict_types=1);

namespace Swoft\Error\Listener;

use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Error\ErrorManager;
use Swoft\Error\ErrorRegister;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use Swoft\SwoftEvent;

/**
 * Class WorkerStartListener
 *
 * @since 2.0
 * @Listener(SwoftEvent::APP_INIT_COMPLETE)
 */
class AppInitCompleteListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws ContainerException
     */
    public function handle(EventInterface $event): void
    {
        /** @var ErrorManager $chain */
        $chain = BeanFactory::getSingleton(ErrorManager::class);

        // Register error handlers
        $count  = ErrorRegister::register($chain);
        $msgTpl = 'Error manager init completed(%d type, %d handler, %d exception)';

        CLog::info($msgTpl, $chain->getTypeCount(), $count, $chain->getCount());
    }
}
