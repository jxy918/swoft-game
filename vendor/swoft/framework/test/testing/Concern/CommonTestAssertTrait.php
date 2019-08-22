<?php declare(strict_types=1);

namespace SwoftTest\Testing\Concern;

use PHPUnit\Framework\Assert;
use function in_array;
use function strpos;

/**
 * Class CommonTestAssertTrait
 *
 * @since 2.0
 */
trait CommonTestAssertTrait
{
    /**
     * @param string $haystack
     * @param string $needle
     */
    public function assertContainString(string $haystack, string $needle): void
    {
        $exist = strpos($haystack, $needle) !== false;

        Assert::assertTrue($exist);
    }

    /**
     * @param array $haystack
     * @param mixed $needle
     */
    public function assertArrayContainValue(array $haystack, $needle): void
    {
        $exist = in_array($needle, $haystack, true);

        Assert::assertTrue($exist);
    }
}
