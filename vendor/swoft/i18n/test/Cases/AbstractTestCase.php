<?php

namespace SwoftTest\I18n;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{

    /**
     * @param callable $func
     * @param          $expectedExceptionClass
     */
    protected function assertException(callable $func, string $expectedExceptionClass)
    {
        try {
            value($func);
        } catch (\Exception $e) {
            $this->assertEquals($expectedExceptionClass, \get_class($e));
        }
    }

    /**
     * @param callable $func
     * @param          $expectedErrorClass
     */
    protected function assertError(callable $func, string $expectedErrorClass)
    {
        try {
            value($func);
        } catch (\Throwable $t) {
            $this->assertEquals($expectedErrorClass, \get_class($t));
        }
    }

}