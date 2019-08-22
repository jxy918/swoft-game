<?php declare(strict_types=1);

namespace SwoftTest\Http\Message\Unit;

use PHPUnit\Framework\TestCase;
use Swoft\Http\Message\PsrRequest;

class PsrRequestTest extends TestCase
{
    public function testHeaders(): void
    {
        $r = new PsrRequest();
        $r = $r->withHeaders([
            'accept-language' => 'zh-CN, zh;q=0.8, en;q=0.5',
        ]);

        $this->assertSame('zh-CN, zh;q=0.8, en;q=0.5', $r->getHeaderLine('Accept-Language'));
        $this->assertSame('zh-CN, zh;q=0.8, en;q=0.5', $r->getHeaderLine('accept-language'));

        // GetAcceptLanguages
        $ls = $r->getAcceptLanguages();
        $this->assertNotEmpty($ls);
        $this->assertSame(['zh-CN', 1.0], $ls[0]);
        $this->assertSame(['zh', 0.8], $ls[1]);
        $this->assertSame(['en', 0.5], $ls[2]);
    }
}
