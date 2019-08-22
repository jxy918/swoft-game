<?php declare(strict_types=1);

namespace Swoft\Error;

/**
 * Class ErrorType
 *
 * @since 2.0
 */
final class ErrorType
{
    public const CLI  = 2;
    public const RPC  = 3;
    public const UDP  = 4;
    public const SOCK = 7;
    public const TASK = 8;

    public const WORKER = 9;

    // HTTP server
    public const HTTP = 16;

    // WebSocket server
    public const WS_HS  = 21;
    public const WS_OPN = 22;
    public const WS_MSG = 23;
    public const WS_CLS = 24;

    // Tcp server
    public const TCP_CNT = 31;
    public const TCP_RCV = 32;
    public const TCP_CLS = 33;

    public const SYS = 85;

    // default
    public const DEF     = 90;
    public const DEFAULT = 90;
}
