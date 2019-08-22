<?php declare(strict_types=1);

namespace Swoft\Http\Server;

/**
 * Class HttpServerEvent
 *
 * @since 2.0
 */
class HttpServerEvent
{
    /**
     * Before request event
     */
    public const BEFORE_REQUEST = 'swoft.http.server.request.before';

    /**
     * After request event
     */
    public const AFTER_REQUEST = 'swoft.http.server.request.after';
}