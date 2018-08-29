<?php

namespace Swoft\Session\Handler;

use Swoft\Http\Message\Server\Request;

/**
 * Interface NeedRequestInterface
 *
 * @package Swoft\Session\Handler
 */
interface NeedRequestInterface
{

    /**
     * @return Request
     */
    public function getRequest(): Request;

    /**
     * @param Request $request
     * @return static
     */
    public function setRequest(Request $request);

}