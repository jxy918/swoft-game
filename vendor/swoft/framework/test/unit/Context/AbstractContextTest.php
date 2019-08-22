<?php declare(strict_types=1);

namespace SwoftTest\Unit\Context;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use Swoft\Context\AbstractContext;

/**
 * Class AbstractContextTest
 */
class AbstractContextTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testData(): void
    {
        $stub = $this->getMockForAbstractClass(AbstractContext::class);
        $stub->set('key0', 'val0');
        $stub->setMulti(['key1' => 'val1']);

        $this->assertTrue($stub->has('key0'));
        $this->assertFalse($stub->has('keyN'));
        $this->assertNotEmpty($stub->getData());
        $this->assertCount(2, $stub->getData());
        $this->assertSame('val0', $stub->get('key0'));
        $this->assertSame('val1', $stub->get('key1'));

        $stub->unset('key0');
        $this->assertFalse($stub->has('key0'));

        $stub->set('key.sub0', 'val0');
        $this->assertSame('val0', $stub->get('key.sub0'));

        $stub->clear();
        $this->assertEmpty($stub->getData());
    }
}
