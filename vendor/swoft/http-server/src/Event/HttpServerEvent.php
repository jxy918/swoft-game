<?php

namespace Swoft\Http\Server\Event;

/**
 * the event of http server
 *
 * @uses      HttpServerEvent
 * @version   2018年01月08日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class HttpServerEvent
{
    /**
     * before request
     */
    const BEFORE_REQUEST = 'beforeRequest';

    /**
     * after request
     */
    const AFTER_REQUEST = 'afterRequest';
}
