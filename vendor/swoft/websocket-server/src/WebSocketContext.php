<?php

namespace Swoft\WebSocket\Server;

use Psr\Http\Message\RequestInterface;
use Swoft\Core\Coroutine;
use Swoft\Http\Message\Server\Request;

/**
 * Class WebSocketContext
 * @package Swoft\WebSocket\Server
 */
class WebSocketContext
{
    const META_KEY = 'meta';
    const REQUEST_KEY = 'request';

    /**
     * @var array
     * [
     *  fd => [
            // metadata
     *      'meta' => [
     *          'fd' => fd,
     *          'path' => request path,
     *          ...
     *      ],
     *
     *      'request' => swoft psr7 request
     *  ]
     * ]
     */
    private static $connections = [];

    /**
     * The map for coroutine id to fd
     * @var array
     * [ coID => fd ]
     */
    private static $map = [];

    /**
     * @see WebSocketEventTrait::onHandShake()
     * @param int $fd
     * @param array $meta
     * [
     *  'path' => request uri path,
     * ]
     * @param Request $request
     */
    public static function init(int $fd, array $meta, Request $request)
    {
        self::$connections[$fd][self::META_KEY] = $meta;
        self::$connections[$fd][self::REQUEST_KEY] = $request;
    }

    /**
     * @param int $fd
     * @param string $ctxKey
     * @param mixed $ctxValue
     */
    public static function set(int $fd, string $ctxKey, $ctxValue)
    {
        self::$connections[$fd][$ctxKey] = $ctxValue;
    }

    /**
     * @param int $fd
     * @param string|null $ctxKey
     * @return array|null
     */
    public static function get(int $fd = null, string $ctxKey = null)
    {
        if ($fd === null && !($fd = self::getFdByCoId())) {
            return null;
        }

        if ($ctxKey) {
            return self::getContext($ctxKey, $fd);
        }

        return self::$connections[$fd] ?? null;
    }

    /**
     * @param int $fd
     * @return bool
     */
    public static function has(int $fd): bool
    {
        return isset(self::$connections[$fd]);
    }

    /**
     * @param int|null $fd
     * @return null
     */
    public static function del(int $fd = null)
    {
        if ($fd === null && !($fd = self::getFdByCoId())) {
            return false;
        }

        if (isset(self::$connections[$fd])) {
            unset(self::$connections[$fd]);
            return true;
        }

        return false;
    }

    /**
     * @param string $ctxKey
     * @param int|null $fd
     * @return mixed|null
     */
    public static function getContext(string $ctxKey, int $fd = null)
    {
        if ($fd === null && !($fd = self::getFdByCoId())) {
            return null;
        }

        return self::$connections[$fd][$ctxKey] ?? null;
    }

    /**
     * @param string $ctxKey
     * @param int $fd
     * @return bool
     */
    public static function hasContext(string $ctxKey, int $fd = null): bool
    {
        if ($fd === null && !($fd = self::getFdByCoId())) {
            return false;
        }

        return isset(self::$connections[$fd][$ctxKey]);
    }

    /**
     * @return int
     */
    public static function count(): int
    {
        return \count(self::$connections);
    }

    /**
     * @return array|null
     */
    public static function getByCoId()
    {
        $fd = self::getFdByCoId();

        if ($fd === null) {
            return null;
        }

        return self::get($fd);
    }

    /**
     * @return Request|null
     */
    public static function getRequest()
    {
        return self::getCoroutineContext(self::REQUEST_KEY);
    }

    /**
     * Set the object of request
     *
     * @param RequestInterface|Request $request
     */
    public static function setRequest(RequestInterface $request)
    {
        $fd = self::getFdByCoId();

        self::$connections[$fd][self::REQUEST_KEY] = $request;
    }

    /**
     * @param string|null $key
     * @param int|null $fd
     * @return array|mixed
     */
    public static function getMeta(string $key = null, int $fd = null)
    {
        $meta = self::getCoroutineContext(self::META_KEY, $fd);

        if ($key === null) {
            return $meta;
        }

        if (!$meta) {
            return null;
        }

        // find value by key in meta
        return $meta[$key] ?? null;
    }

    /**
     * @param int $fd
     * @param mixed $value
     * @param string|null $key The key of the meta
     */
    public static function setMeta(int $fd, $value, string $key = null)
    {
        if ($key !== null) {
            $meta = self::getCoroutineContext(self::META_KEY);
            $meta[$key] = $value;
            // override
            $value = $meta;
        }

        // setting
        self::$connections[$fd][self::META_KEY] = $value;
    }

    /**
     * @param string $key
     * @param int|null $fd
     * @return mixed|null
     */
    public static function getCoroutineContext(string $key, int $fd = null)
    {
        $fd = $fd ?? self::getFdByCoId();

        // find context
        if (!$context = self::$connections[$fd] ?? null) {
            return null;
        }

        // find value by key in context
        return $context[$key] ?? null;
    }

    /**
     * init coId to fd mapping
     * @param int $fd
     */
    public static function setFdToCoId(int $fd)
    {
        $cid = self::getCoroutineId();

        self::$map[$cid] = $fd;
    }

    /**
     * @return int|null
     */
    public static function getFdByCoId()
    {
        $cid = self::getCoroutineId();

        return self::$map[$cid] ?? null;
    }

    /**
     * delete coId to fd mapping
     * @param int|null $cid
     * @return bool
     */
    public static function delFdByCoId(int $cid = null): bool
    {
        $cid = $cid > -1 ? $cid : self::getCoroutineId();

        if (isset(self::$map[$cid])) {
            unset(self::$map[$cid]);

            return true;
        }

        return false;
    }

    /**
     * Get current coroutine ID
     *
     * @return int|null Return null when in non-coroutine context
     */
    public static function getCoroutineId()
    {
        return Coroutine::tid();
    }

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return self::$map;
    }

    /**
     * @return array
     */
    public static function getConnections(): array
    {
        return self::$connections;
    }
}
