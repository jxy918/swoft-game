<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace SwoftTest\Helper;


use Swoft\Helper\XmlHelper;
use SwoftTest\AbstractTestCase;

class XmlHelperTest extends AbstractTestCase
{
    /**
     * @test
     * @covers XmlHelper::encode()
     */
    public function testEncode()
    {
        $data = ['touser' => ['hello' => 'world'], 'test' => 1];
        $res = XmlHelper::encode($data);
        $this->assertStringStartsWith('<xml><touser>', $res);
        $this->assertStringEndsWith('</test></xml>', $res);
    }

    /**
     * @test
     * @covers XmlHelper::decode()
     */
    public function testDecode()
    {
        $xml = '<xml><touser><hello><![CDATA[world]]></hello></touser><test>1</test></xml>';
        $res = XmlHelper::decode($xml);
        $this->assertArraySubset(['touser' => ['hello' => 'world']], $res);
    }
}
