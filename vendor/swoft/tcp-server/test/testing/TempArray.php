<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Testing;

use function array_unshift;
use function json_encode;

/**
 * Class TempArray - This is an tool class for testing.
 */
class TempArray
{
    /**
     * @var array
     */
    private static $array = [];

    /**
     * @param int|string $key
     *
     * @return bool
     */
    public static function has($key): bool
    {
        return isset(self::$array[$key]);
    }

    /**
     * @param int|string $key
     *
     * @return mixed|null
     */
    public static function get($key)
    {
        return self::$array[$key] ?? null;
    }

    /**
     * @param int|array $key
     * @param mixed     $value
     */
    public static function set($key, $value): void
    {
        self::$array[$key] = $value;
    }

    /**
     * @param int|string $key
     */
    public static function del($key): void
    {
        if (isset(self::$array[$key])) {
            unset(self::$array[$key]);
        }
    }

    /**
     * @param mixed $value
     */
    public static function append($value): void
    {
        self::$array[] = $value;
    }

    /**
     * @param mixed $value
     */
    public static function prepend($value): void
    {
        array_unshift(self::$array, $value);
    }

    /**
     * @return string
     */
    public static function toString(): string
    {
        return (string)json_encode(self::$array);
    }

    /**
     * @return array
     */
    public static function getArray(): array
    {
        return self::$array;
    }

    /**
     * @param array $array
     */
    public static function setArray(array $array): void
    {
        self::$array = $array;
    }

    /**
     * clear array
     */
    public static function clear(): void
    {
        self::$array = [];
    }

    /**
     * @return array
     */
    public static function getAllAndClear(): array
    {
        $tempArray   = self::$array;
        self::$array = [];

        return $tempArray;
    }
}
