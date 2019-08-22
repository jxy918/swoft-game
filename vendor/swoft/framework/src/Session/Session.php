<?php declare(strict_types=1);

namespace Swoft\Session;

use Swoft\Co;
use Swoft\Exception\SessionException;
use Swoft\WebSocket\Server\Connection;

/**
 * Class Session - Global session manage
 *
 * @since 2.0
 */
class Session
{
    /**
     * The map for coroutineID to SessionID
     *
     * @var array [ CID => SID ]
     */
    private static $idMap = [];

    /**
     * Session connection list
     *
     * @var SessionInterface[]
     *
     * @example
     * [
     *      // Such as webSocket connection
     *      'fd'  => SessionInterface,
     *      'fd2' => SessionInterface,
     *      'fd3' => SessionInterface,
     *      // Such as http session
     *      'sess id' => SessionInterface,
     * ]
     */
    private static $sessions = [];

    /*****************************************************************************
     * SID and CID relationship manage
     ****************************************************************************/

    /**
     * Bind current coroutine to an session
     *  In webSocket server, will bind FD and CID relationship. (should call it on handshake, message, open, close)
     *  In Http application, will bind session Id and cid relationship. (call on request)
     *
     * @param string $sid
     */
    public static function bindCo(string $sid): void
    {
        self::$idMap[Co::tid()] = $sid;
    }

    /**
     * Unbind SID and CID relationship. (should call it on complete OR error)
     *
     * @return string
     */
    public static function unbindCo(): string
    {
        $sid = '';
        $tid = Co::tid();

        if (isset(self::$idMap[$tid])) {
            $sid = self::$idMap[$tid];
            unset(self::$idMap[$tid]);
        }

        return $sid;
    }

    /**
     * @return string
     */
    public static function getBoundedSid(): string
    {
        $tid = Co::tid();
        return self::$idMap[$tid] ?? '';
    }

    /*****************************************************************************
     * Session manage
     ****************************************************************************/

    /**
     * Check session has exist on current worker
     *
     * @param string $sid
     *
     * @return bool
     */
    public static function has(string $sid): bool
    {
        return isset(self::$sessions[$sid]);
    }

    /**
     * Get session by FD
     *
     * @param string $sid If not specified, return the current corresponding session
     * @return SessionInterface|Connection
     */
    public static function get(string $sid = ''): ?SessionInterface
    {
        $sid = $sid ?: self::getBoundedSid();

        return self::$sessions[$sid] ?? null;
    }

    /**
     * Get connection by FD. if not found will throw exception.
     *
     * @param string $sid
     * @return SessionInterface|Connection
     */
    public static function mustGet(string $sid = ''): SessionInterface
    {
        $sid = $sid ?: self::getBoundedSid();

        if (isset(self::$sessions[$sid])) {
            return self::$sessions[$sid];
        }

        throw new SessionException('session information has been lost of the SID: ' . $sid);
    }

    /**
     * Set Session connection
     *
     * @param string           $sid On websocket server, sid is connection fd.
     * @param SessionInterface $session
     */
    public static function set(string $sid, SessionInterface $session): void
    {
        self::$sessions[$sid] = $session;

        // Bind cid => sid(fd)
        self::bindCo($sid);
    }

    /**
     * Destroy session
     *
     * @param string $sid If empty, destroy current CID relationship session
     */
    public static function destroy(string $sid = ''): void
    {
        $sid = $sid ?: self::getBoundedSid();

        if (isset(self::$sessions[$sid])) {
            // Clear self data.
            self::$sessions[$sid]->clear();
            unset(self::$sessions[$sid], $conn);
        }
    }

    /**
     * Clear all
     */
    public static function clear(): void
    {
        self::$idMap = self::$sessions = [];
    }

    /**
     * @return array
     */
    public static function getIdMap(): array
    {
        return self::$idMap;
    }

    /**
     * @return SessionInterface[]
     */
    public static function getSessions(): array
    {
        return self::$sessions;
    }
}
