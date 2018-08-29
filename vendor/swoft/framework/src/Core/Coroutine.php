<?php

namespace Swoft\Core;

use Swoft\App;
use Swoft\Helper\PhpHelper;
use Swoole\Coroutine as SwCoroutine;

/**
 * @uses \Swoft\Core\Coroutine
 */
class Coroutine
{
    /**
     * @var int
     */
    private static $tid = -1;

    /**
     * Coroutine id mapping
     *
     * @var array
     * [
     *  child id => top id,
     *  child id => top id,
     *  ... ...
     * ]
     */
    private static $idMap = [];

    /**
     * Get the current coroutine ID,
     * Return null when running in non-coroutine context
     *
     * @return int|null
     */
    public static function id()
    {
        $cid = SwCoroutine::getuid();
        if ($cid !== -1) {
            return $cid;
        }

        return self::$tid;
    }

    /**
     * Get the top coroutine ID,
     * Return null when running in non-coroutine context
     *
     * @return int|null
     */
    public static function tid()
    {
        $id = self::id();
        return self::$idMap[$id] ?? $id;
    }

    /**
     * Create a coroutine
     *
     * @param callable $cb
     *
     * @return bool
     */
    public static function create(callable $cb)
    {
        $tid = self::tid();
        return SwCoroutine::create(function () use ($cb, $tid) {
            $id = SwCoroutine::getuid();
            self::$idMap[$id] = $tid;

            PhpHelper::call($cb);
        });
    }

    /**
     * Suspend a coroutine
     *
     * @param string $corouindId
     */
    public static function suspend($corouindId)
    {
        SwCoroutine::suspend($corouindId);
    }

    /**
     * Resume a coroutine
     *
     * @param string $coroutineId
     */
    public static function resume($coroutineId)
    {
        SwCoroutine::resume($coroutineId);
    }

    /**
     * Is Support Coroutine
     * Since swoole v2.0.11, use coroutine client in cli mode is available
     *
     * @return bool
     */
    public static function isSupportCoroutine(): bool
    {
        if (swoole_version() >= '2.0.11') {
            return true;
        } else {
            return App::isWorkerStatus();
        }
    }

    /**
     * Determine if should create a coroutine when you
     * want to use a Coroutine Client, and you should
     * always use self::isSupportCoroutine() before
     * call this method.
     *
     * @return bool
     */
    public static function shouldWrapCoroutine()
    {
        return App::isWorkerStatus() && swoole_version() >= '2.0.11';
    }
}
