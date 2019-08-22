<?php declare(strict_types=1);


namespace Swoft\Log\Helper;


use ReflectionException;
use function sprintf;
use Swoft\Bean\BeanFactory;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Log\Logger;

class Log
{
    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function emergency(string $message, ...$params): bool
    {
        if (!empty($params)) {
            $message = sprintf($message, ...$params);
        }

        return self::getLogger()->emergency($message);
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function debug(string $message, ...$params): bool
    {
        if (!empty($params)) {
            $message = sprintf($message, ...$params);
        }

        if (APP_DEBUG) {
            return self::getLogger()->debug($message);
        }

        return true;
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function alert(string $message, ...$params): bool
    {
        if (!empty($params)) {
            $message = sprintf($message, ...$params);
        }

        return self::getLogger()->alert($message);
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function info(string $message, ...$params): bool
    {
        if (!empty($params)) {
            $message = sprintf($message, ...$params);
        }

        return self::getLogger()->info($message);
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function warning(string $message, ...$params): bool
    {
        if (!empty($params)) {
            $message = sprintf($message, ...$params);
        }

        return self::getLogger()->warning($message);
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return bool
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function error(string $message, ...$params): bool
    {
        if (!empty($params)) {
            $message = sprintf($message, ...$params);
        }

        return self::getLogger()->error($message);
    }

    /**
     * Push log
     *
     * @param string $key
     * @param mixed  $val
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function pushLog(string $key, $val): void
    {
        self::getLogger()->pushLog($key, $val);
    }

    /**
     * Profile start
     *
     * @param string $name
     * @param array  $params
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function profileStart(string $name, ...$params): void
    {
        if (!empty($params)) {
            $name = sprintf($name, ...$params);
        }

        self::getLogger()->profileStart($name);
    }

    /**
     * @param string   $name
     * @param int      $hit
     * @param int|null $total
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function counting(string $name, int $hit, int $total = null): void
    {
        self::getLogger()->counting($name, $hit, $total);
    }

    /**
     * Profile end
     *
     * @param string $name
     * @param array  $params
     *
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function profileEnd(string $name, ...$params): void
    {
        if (!empty($params)) {
            $name = sprintf($name, ...$params);
        }

        self::getLogger()->profileEnd($name);
    }

    /**
     * @return Logger
     * @throws ReflectionException
     * @throws ContainerException
     */
    public static function getLogger(): Logger
    {
        return BeanFactory::getBean('logger');
    }
}
