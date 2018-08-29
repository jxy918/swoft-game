<?php

namespace Swoft\Event\Listeners;

use Swoft\App;
use Swoft\Bean\Annotation\Listener;
use Swoft\Event\AppEvent;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;

/**
 * 应用加载事件
 *
 * @Listener(AppEvent::APPLICATION_LOADER)
 * @uses      ApplicationLoaderListener
 * @version   2017年09月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ApplicationLoaderListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event 事件对象
     * @return void
     */
    public function handle(EventInterface $event)
    {
        App::setProperties();
    }
}
