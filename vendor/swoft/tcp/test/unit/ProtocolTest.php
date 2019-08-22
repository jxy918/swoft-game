<?php declare(strict_types=1);

namespace SwoftTest\Tcp\Unit;

use function get_class;
use PHPUnit\Framework\TestCase;
use Swoft\Tcp\Exception\ProtocolException;
use Swoft\Tcp\Packer\JsonPacker;
use Swoft\Tcp\Packer\PhpPacker;
use Swoft\Tcp\Packer\SimpleTokenPacker;
use Swoft\Tcp\Protocol;
use SwoftTest\Testing\Concern\CommonTestAssertTrait;
use Throwable;

/**
 * Class ProtocolTest
 */
class ProtocolTest extends TestCase
{
    use CommonTestAssertTrait;

    public function testBasic(): void
    {
        $p = new Protocol();

        $this->assertTrue($p->isValidType(SimpleTokenPacker::TYPE));
        $this->assertFalse($p->isValidType('not-exist'));

        $this->assertSame(SimpleTokenPacker::TYPE, $p->getType());
        $this->assertSame(Protocol::EOF_SPLIT, $p->getSplitType());

        $this->assertArrayContainValue($p->getPackerNames(), SimpleTokenPacker::TYPE);

        $ps = $p->getPackers();
        $this->assertArrayHasKey(PhpPacker::TYPE, $ps);
        $this->assertArrayHasKey(JsonPacker::TYPE, $ps);
        $this->assertArrayHasKey(SimpleTokenPacker::TYPE, $ps);

        $this->assertTrue($p->isOpenEofCheck());
        $this->assertFalse($p->isOpenEofSplit());
        $this->assertFalse($p->isOpenLengthCheck());

        $c = $p->getConfig();
        $this->assertArrayHasKey('open_eof_check', $c);
        $this->assertArrayHasKey('open_eof_split', $c);
        $this->assertArrayHasKey('package_eof', $c);
        $this->assertArrayHasKey('package_max_length', $c);
        $this->assertArrayHasKey('open_length_check', $c);
        $this->assertFalse($c['open_length_check']);

        $this->assertSame(81920, $p->getPackageMaxLength());

        // eof
        $this->assertSame(Protocol::DEFAULT_EOF, $p->getPackageEof());

        // len
        $this->assertSame('N', $p->getPackageLengthType());
        $this->assertSame(8, $p->getPackageLengthOffset());
        $this->assertSame(16, $p->getPackageBodyOffset());
    }

    public function testGetPackerClass(): void
    {
        $p = new Protocol();

        $this->assertSame(SimpleTokenPacker::class, $p->getPackerClass());
        $this->assertSame(JsonPacker::class, $p->getPackerClass(JsonPacker::TYPE));

        try {
            $p->getPackerClass('not-exist');
        } catch (Throwable $e) {
            $this->assertSame(ProtocolException::class, get_class($e));
            $this->assertSame('The data packer(type: not-exist) is not exist!', $e->getMessage());
        }

        $p->setPacker('new-packer', 'some class');
        $this->assertSame('some class', $p->getPackerClass('new-packer'));
    }
}
