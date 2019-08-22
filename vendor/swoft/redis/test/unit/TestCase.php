<?php declare(strict_types=1);


namespace SwoftTest\Redis\Unit;

use Swoft\Redis\Redis;

/**
 * Class TestCase
 *
 * @since 2.0
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $value
     *
     * @return string
     */
    public function setKey(string $value): string
    {
        $key = uniqid();
        Redis::set($key, $value);

        return $key;
    }
}