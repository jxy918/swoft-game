<?php declare(strict_types=1);


namespace Swoft;

use ReflectionException;
use Swoft;
use Swoft\Log\Error;
use Swoft\Log\Helper\Log;
use Swoft\Stdlib\Helper\PhpHelper;
use Swoole\Timer as SwooleTimer;
use Throwable;

/**
 * Class Timer
 *
 * @since 2.0
 */
class Timer
{
    /**
     * @param int            $msec
     * @param array|callable $callback
     * @param array          ...$params
     *
     * @return int
     * @throws Bean\Exception\ContainerException
     * @throws Exception\SwoftException
     * @throws ReflectionException
     */
    public static function tick(int $msec, $callback, ...$params): int
    {
        $items = self::getLogItems();
        return SwooleTimer::tick($msec, function (int $timerId, ...$params) use ($callback, $items) {

            try {
                // Before
                Swoft::trigger(SwoftEvent::TIMER_TICK_BEFORE, null, $timerId, $params);

                // Init Context
                self::initItems($items);

                // Callback
                PhpHelper::call($callback, $timerId, ... $params);

                // After
                Swoft::trigger(SwoftEvent::TIMER_TICK_AFTER);
            } catch (Throwable $e) {
                Error::log('Timer tick error！%s %s %d', $e->getMessage(), $e->getFile(), $e->getLine());
            }
        }, ... $params);
    }

    /**
     * @param int            $msec
     * @param array|callable $callback
     * @param array          ...$params
     *
     * @return int
     * @throws Bean\Exception\ContainerException
     * @throws Exception\SwoftException
     * @throws ReflectionException
     */
    public static function after(int $msec, $callback, ...$params): int
    {
        $items = self::getLogItems();
        return SwooleTimer::after($msec, function () use ($callback, $items) {

            try {
                // Before
                Swoft::trigger(SwoftEvent::TIMER_AFTER_BEFORE);

                // Init Context
                self::initItems($items);

                // Callback
                PhpHelper::call($callback, 1);

                // After
                Swoft::trigger(SwoftEvent::TIMER_AFTER_AFTER);
            } catch (Throwable $e) {
                Error::log('Timer after error！%s %s %d', $e->getMessage(), $e->getFile(), $e->getLine());
            }
        }, ... $params);
    }

    /**
     * @param int $timerId
     *
     * @return bool
     */
    public static function clear(int $timerId): bool
    {
        return SwooleTimer::clear($timerId);
    }

    /**
     * @return bool
     */
    public static function clearAll(): bool
    {
        return SwooleTimer::clearAll();
    }

    /**
     * @param int $timerId
     *
     * @return array
     */
    public static function info(int $timerId): array
    {
        return SwooleTimer::info($timerId);
    }

    /**
     * @return SwooleTimer\Iterator
     */
    public static function list(): SwooleTimer\Iterator
    {
        return SwooleTimer::list();
    }

    /**
     * @return array
     */
    public static function stats(): array
    {
        return SwooleTimer::stats();
    }

    /**
     * @return array
     * @throws Bean\Exception\ContainerException
     * @throws Exception\SwoftException
     * @throws ReflectionException
     */
    private static function getLogItems(): array
    {
        $data  = [];
        $items = Log::getLogger()->getItems();

        foreach ($items as $item) {
            $data[$item] = context()->get($item, '');
        }

        return $data;
    }

    /**
     * @param array $items
     *
     * @throws Exception\SwoftException
     */
    private static function initItems(array $items): void
    {
        foreach ($items as $key => $value) {
            context()->set($key, (string)$value);
        }
    }
}