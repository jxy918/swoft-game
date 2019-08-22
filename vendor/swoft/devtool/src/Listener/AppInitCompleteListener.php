<?php declare(strict_types=1);

namespace Swoft\Devtool\Listener;

use Swoft\Bean\BeanFactory;
use Swoft\Devtool\Model\Logic\MetaLogic;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use Swoft\SwoftEvent;
use Throwable;

/**
 * Class AppInitCompleteListener
 *
 * @since 2.0
 *
 * @Listener(event=SwoftEvent::APP_INIT_COMPLETE)
 */
class AppInitCompleteListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     *
     * @throws Throwable
     */
    public function handle(EventInterface $event): void
    {
        // Generate phpstorm.meta.php
        if (APP_DEBUG) {
            CLog::debug('auto generate phpstorm meta file');

            $phpstorm = BeanFactory::getBean(MetaLogic::class);
            $phpstorm->generate();
        }
    }
}
