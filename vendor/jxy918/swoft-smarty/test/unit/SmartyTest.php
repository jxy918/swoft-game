<?php

namespace SwoftTest\Smarty;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Swoft\Smarty\Smarty;
use function dirname;

/**
 * Class SmartyTest
 */
class SmartyTest extends TestCase
{
    public function testSmarty(): void
    {

        $r = new Smarty();
        $r->initView();

        $this->assertSame(true, $r->getDebugging());
        $this->assertSame(true, $r->getCaching());
        $this->assertSame(120, $r->getCacheLifetime());
        $this->assertSame('<!--{', $r->getLeftDelimiter());
        $this->assertSame('}-->', $r->getRightDelimiter());
        $this->assertSame(\Swoft::getAlias('@base/resource/template'), $r->getTemplateDir());
        $this->assertSame(\Swoft::getAlias('@base/runtime/template_c'), $r->getCompileDir());
        $this->assertSame(\Swoft::getAlias('@base/runtime/cache'), $r->getCacheDir());
    }
}
