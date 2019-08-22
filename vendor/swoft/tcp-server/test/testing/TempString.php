<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Server\Testing;

use function implode;

/**
 * Class TempString - This is an tool class for testing.
 */
class TempString
{
    /**
     * @var string
     */
    private static $string = '';

    /**
     * @return string
     */
    public static function get(): string
    {
        return self::$string;
    }

    /**
     * @param string $string
     */
    public static function set(string $string): void
    {
        self::$string = $string;
    }

    /**
     * @param string[] $string
     */
    public static function add(string ...$string): void
    {
        self::$string .= implode(' ', $string);
    }

    /**
     * @param string $string
     */
    public static function append(string $string): void
    {
        self::$string = $string . self::$string;
    }

    /**
     * @param string $string
     */
    public static function prepend(string $string): void
    {
        self::$string .= $string;
    }

    /**
     * clear string
     */
    public static function clear(): void
    {
        self::$string = '';
    }

    /**
     * Clear string and return string
     *
     * @return string
     */
    public static function getAndClear(): string
    {
        $tempString   = self::$string;
        self::$string = '';

        return $tempString;
    }
}
