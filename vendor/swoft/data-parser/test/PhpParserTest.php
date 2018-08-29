<?php

namespace SwoftTest\DataParser;

use Swoft\DataParser\PhpParser;
use PHPUnit\Framework\TestCase;

/**
 * Class PhpParserTest
 * @covers PhpParser
 */
class PhpParserTest extends TestCase
{
    public function testDecode()
    {
        $str = 'a:1:{s:4:"name";s:5:"value";}';

        $parser = new PhpParser();
        $ret = $parser->decode($str);

        $this->assertInternalType('array', $ret);
        $this->assertArrayHasKey('name', $ret);
    }

    public function testEncode()
    {
        $data = [
            'name' => 'value',
        ];

        $parser = new PhpParser();
        $ret = $parser->encode($data);

        $this->assertInternalType('string', $ret);
        $this->assertStringStartsWith('a:1:{', $ret);
    }
}
