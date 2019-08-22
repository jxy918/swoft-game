<?php declare(strict_types=1);

namespace Swoft\Tcp\Server;

/**
 * Class TcpServerEvent
 *
 * @since 2.0
 */
final class TcpServerEvent
{
    /**
     * On connect
     */
    public const CONNECT = 'swoft.tcp.server.connect';

    /**
     * On connect error
     */
    public const CONNECT_ERROR = 'swoft.tcp.server.connect.error';

    /**
     * On receive
     */
    public const RECEIVE = 'swoft.tcp.server.receive';

    /**
     * On package send
     */
    public const PACKAGE_SEND = 'swoft.tcp.server.package.send';

    /**
     * On receive error
     */
    public const RECEIVE_ERROR = 'swoft.tcp.server.receive.error';

    /**
     * On receive after
     */
    public const AFTER_RECEIVE = 'swoft.tcp.server.receive.after';

    /**
     * On close
     */
    public const CLOSE = 'swoft.tcp.server.close';

    /**
     * On close error
     */
    public const CLOSE_ERROR = 'swoft.tcp.server.close.error';
}
