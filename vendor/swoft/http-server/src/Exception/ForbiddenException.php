<?php

namespace Swoft\Http\Server\Exception;

/**
 * @uses      ForbiddenException
 * @version   2017-11-11
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ForbiddenException extends HttpException
{
    protected $code = 403;
}
