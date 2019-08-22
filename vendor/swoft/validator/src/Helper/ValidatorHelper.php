<?php declare(strict_types=1);


namespace Swoft\Validator\Helper;

use function in_array;
use function mb_strlen;
use function preg_match;

/**
 * Class ValidatorHelper
 *
 * @since 2.0
 */
class ValidatorHelper
{
    /**
     * @var string
     */
    private static $mobilePattern = '/^1\d{10}$/';

    /**
     * @var string
     */
    private static $emailPattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/';

    /**
     * @var string
     */
    private static $ipPattern = '/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/';

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function validateEmail(string $value): bool
    {
        if (!preg_match(self::$emailPattern, $value)) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $value
     * @param array $values
     *
     * @return bool
     */
    public static function validateEnum($value, array $values): bool
    {
        if (in_array($value, $values)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function validateIp(string $value): bool
    {
        if (!preg_match(self::$ipPattern, $value)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $value
     * @param int    $min
     * @param int    $max
     *
     * @return bool
     */
    public static function validatelength(string $value, int $min, int $max): bool
    {
        $length = mb_strlen($value);
        if ($length < $min || $length > $max) {
            return false;
        }

        return true;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function validateMobile(string $value): bool
    {
        if (!preg_match(self::$mobilePattern, $value)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $value
     * @param string $regex
     *
     * @return bool
     */
    public static function validatePattern(string $value, string $regex): bool
    {
        if (!preg_match($regex, $value)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $value
     * @param int $min
     * @param int $max
     *
     * @return bool
     */
    public static function validateRange(int $value, int $min, int $max): bool
    {
        if ($value < $min || $value > $max) {
            return false;
        }

        return true;
    }
}