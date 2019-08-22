<?php declare(strict_types=1);

namespace SwoftTest\Event\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Swoft\Event\Event;

/**
 * Class EventTest
 */
class EventTest extends TestCase
{
    public function testEvent(): void
    {
        $e = new Event('test', [
            'val0',
            'key1' => 'val1',
        ]);

        // name
        $this->assertSame('test', $e->getName());

        // target
        $this->assertNull($e->getTarget());
        $e->setTarget('target');
        $this->assertSame('target', $e->getTarget());

        // params
        $this->assertNotEmpty($e->getParams());

        // get param
        $this->assertSame('val0', $e->getParam(0));
        $this->assertSame('val1', $e->getParam('key1'));
        $this->assertNull($e->getParam('not-exist'));
        $this->assertSame('def-val', $e->getParam('not-exist', 'def-val'));

        // set param
        $this->assertFalse($e->hasParam('key2'));
        $e->setParam('key2', 'val2');
        $this->assertTrue($e->hasParam('key2'));

        // add param
        $this->assertFalse($e->hasParam('key3'));
        $e->addParam('key3', 'val3');
        $this->assertTrue($e->hasParam('key3'));
        $e->removeParam('key3');
        $this->assertFalse($e->hasParam('key3'));

        // set params
        $e->setParams([
            'key' => 'val'
        ]);
        $this->assertCount(1, $e->getParams());

        // add params
        $e->addParams([
            'key1' => 'val1'
        ]);
        $this->assertCount(2, $e->getParams());

        // isPropagationStopped
        $this->assertFalse($e->isPropagationStopped());
        $e->stopPropagation(true);
        $this->assertTrue($e->isPropagationStopped());

        $ne = Event::create();
        $this->assertSame('', $ne->getName());

        // serialize
        $data = $e->serialize();
        // unserialize
        $ne->unserialize($data);
        $this->assertSame('test', $ne->getName());

        $e->clearParams();
        $this->assertEmpty($e->getParams());
    }

    public function testBadName(): void
    {
        $e = new Event();

        $this->expectException(InvalidArgumentException::class);
        $e->setName('');

        $this->expectException(InvalidArgumentException::class);
        $e->setParam(null, 'val');
    }
}
