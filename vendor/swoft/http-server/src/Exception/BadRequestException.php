<?php

namespace Swoft\Http\Server\Exception;

/**
 * @uses      BadRequestException
 * @version   2017-11-11
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BadRequestException extends HttpException
{
    protected $code = 400;
}
