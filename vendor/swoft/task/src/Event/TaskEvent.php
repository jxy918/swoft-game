<?php

namespace Swoft\Task\Event;

/**
 *
 *
 * @uses      TaskEvent
 * @version   2018年01月13日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class TaskEvent
{
    /**
     * before task
     */
    const BEFORE_TASK = "beforeTask";

    /**
     * after task
     */
    const AFTER_TASK = "afterTask";

    /**
     * Task finish event
     */
    const FINISH_TASK = 'finish';
}