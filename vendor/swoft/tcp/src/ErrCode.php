<?php declare(strict_types=1);

namespace Swoft\Tcp;

/**
 * Class ErrCode
 *
 * @since 2.0.4
 */
class ErrCode
{
    public const SERVER_ERROR    = -10401;
    public const UNPACKING_FAIL  = -10402;
    public const ROUTE_NOT_FOUND = -10403;
}
