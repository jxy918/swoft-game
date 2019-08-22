<?php declare(strict_types=1);

namespace SwoftTest\Error\Unit;

use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Swoft\Error\ErrorManager;
use SwoftTest\Error\Testing\CustomErrorHandler;

/**
 * Class ErrorHandlersTest
 */
class ErrorHandlersTest extends TestCase
{
    public function testGeneric(): void
    {
        $e  = new RuntimeException('error on runtime');
        $eh = new ErrorManager();

        // null
        $ret = $eh->matchHandler($e);
        $this->assertNull($ret);

        $eh->addHandler(RuntimeException::class, CustomErrorHandler::class);

        $this->assertTrue($eh->getCount() > 0);
        $this->assertTrue($eh->getTypeCount() > 0);

        $ret = $eh->matchHandler($e);
        $this->assertNotEmpty($ret);

        $e1  = new LogicException('logic error');
        $ret = $eh->matchHandler($e1);
        $this->assertNull($ret);

        $eh->clear();

        $this->assertSame(0, $eh->getCount());
    }
}
