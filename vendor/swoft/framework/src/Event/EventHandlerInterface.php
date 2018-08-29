<?php

namespace Swoft\Event;

/**
 * Interface EventHandlerInterface - 独立的事件监听器接口
 * @package Swoft\Event
 * @version   2017年08月30日
 * @author    inhere <in.798@qq.com>
 * @copyright Copyright 2010-2016 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
interface EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event);
}
