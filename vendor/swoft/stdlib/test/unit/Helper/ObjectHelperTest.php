<?php declare(strict_types=1);

namespace SwoftTest\Stdlib\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Swoft\Stdlib\Helper\ObjectHelper;

/**
 * Class ObjectHelperTest
 *
 */
class ObjectHelperTest extends TestCase
{
    public function test(): void
    {
        $samples = [
            'int' => 0,
            'string' => '',
            'float' => 0,
            'bool' => false,
        ];

        foreach ($samples as $sample => $want) {
            $this->assertSame($want, ObjectHelper::getDefaultValue($sample));
        }
    }
}
