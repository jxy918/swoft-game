<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2019-01-08
 * Time: 00:01
 */

namespace Toolkit\CliTest;

use PHPUnit\Framework\TestCase;
use Toolkit\Cli\ColorTag;

/**
 * Class ColorTagTest
 *
 * @package Toolkit\CliTest
 */
class ColorTagTest extends TestCase
{
    public function testMatchAll(): void
    {
        $ret = ColorTag::matchAll('<tag>text0</tag> or <info>text1</info>');
        $this->assertCount(3, $ret);
        // tag
        $this->assertSame('tag', $ret[1][0]);
        $this->assertSame('info', $ret[1][1]);
        // content
        $this->assertSame('text0', $ret[2][0]);

        $ret = ColorTag::matchAll('<some_tag>text</some_tag>');
        $this->assertCount(3, $ret);
        // tag
        $this->assertSame('some_tag', $ret[1][0]);
        // content
        $this->assertSame('text', $ret[2][0]);

        $ret = ColorTag::matchAll('<someTag>text</someTag>');
        $this->assertCount(3, $ret);
        // tag
        $this->assertSame('someTag', $ret[1][0]);
        // content
        $this->assertSame('text', $ret[2][0]);
    }

    public function testStrip(): void
    {
        $text = ColorTag::strip('<tag>text</tag>');
        $this->assertSame('text', $text);

        // no close
        $text = ColorTag::clear('<tag>text<tag>');
        $this->assertSame('<tag>text<tag>', $text);
    }

    public function testWrap(): void
    {
        $text = ColorTag::wrap('text', 'tag');
        $this->assertSame('<tag>text</tag>', $text);

        $text = ColorTag::add('text', '');
        $this->assertSame('text', $text);

        $text = ColorTag::add('', 'tag');
        $this->assertSame('', $text);
    }

    public function testExists(): void
    {
        $this->assertTrue(ColorTag::exists('<tag>text</tag>'));
        $this->assertFalse(ColorTag::exists('text'));
        $this->assertFalse(ColorTag::exists('<tag>text'));
        $this->assertFalse(ColorTag::exists('<tag>text<tag>'));
    }
}
