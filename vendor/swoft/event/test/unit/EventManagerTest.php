<?php declare(strict_types=1);

namespace SwoftTest\Event\Unit;

use PHPUnit\Framework\TestCase;
use Swoft\Event\Event;
use Swoft\Event\EventInterface;
use Swoft\Event\Manager\EventManager;
use Swoft\Event\Manager\EventManagerInterface;
use SwoftTest\Event\Testing\TestHandler;
use SwoftTest\Event\Testing\TestSubscriber;

/**
 * Class EventManagerTest
 */
class EventManagerTest extends TestCase
{
    public function testCreate(): void
    {
        $em = new EventManager();
        $this->assertInstanceOf(EventManagerInterface::class, $em);
        // $this->assertEmpty($em->getParent());
        $this->assertNotEmpty($em->getBasicEvent());
        $this->assertSame(0, $em->countEvents());

        $em->setBasicEvent(new Event('new'));
        $this->assertNotEmpty($evt = $em->getBasicEvent());
        $this->assertSame('new', $evt->getName());
    }

    public function testAttach(): void
    {
        $em = new EventManager();
        $l1 = new TestHandler();
        $em->attach('test', $l1);
        $em->attach('test', function () {
            //
        });

        $this->assertTrue($em->hasListener($l1));
        $this->assertTrue($em->hasListener($l1, 'test'));
        $this->assertTrue($em->isListenedEvent('test'));
        $this->assertCount(2, $em->getEventListeners('test'));
        $this->assertArrayHasKey('test', $em->getListenedEvents(false));

        $em->detach('test', $l1);
        $this->assertCount(1, $em->getEventListeners('test'));
    }

    public function testPriority(): void
    {
        $l0 = new TestHandler();
        $l1 = function () {
            //
        };

        $em = new EventManager();
        $em->attach('test', $l0);
        $em->attach('test', $l1, 5);

        $this->assertEquals(0, $em->getListenerPriority($l0, 'test'));
        $this->assertEquals(5, $em->getListenerPriority($l1, 'test'));
    }

    public function testTrigger(): void
    {
        $l0 = new class
        {
            public function __invoke(Event $evt)
            {
                $evt->addParam('key1', 'val1');
                $evt->setParam('key', 'new val');
            }
        };
        $l1 = function (EventInterface $evt) {
            $evt->setTarget('new target');
        };

        $em = new EventManager();
        $em->attach('test', $l0);
        $em->attach('test', $l1, 5);

        $evt = $em->trigger('test', 'target', ['key' => 'val']);

        $this->assertEquals('new target', $evt->getTarget());
        $this->assertEquals('new val', $evt->getParam('key'));
        $this->assertArrayHasKey('key1', $evt->getParams());
    }

    public function testSubscriber(): void
    {
        $em = new EventManager();
        $em->addListener(TestSubscriber::class);

        $this->assertTrue($em->hasListeners(TestSubscriber::EVENT_ONE));
        $this->assertTrue($em->hasListeners(TestSubscriber::EVENT_TWO));

        $evt = $em->trigger(TestSubscriber::EVENT_ONE);

        $this->assertArrayHasKey('msg', $evt->getParams());
        $this->assertSame(
            'handle the event: test.event1 position: TestSubscriber.handleEvent1()',
            $evt->getParam('msg')
        );
    }
}
