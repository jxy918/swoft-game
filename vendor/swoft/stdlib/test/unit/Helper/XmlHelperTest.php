<?php declare (strict_types=1);

namespace SwoftTest\Stdlib\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Swoft\Stdlib\Helper\XmlHelper;

class XmlHelperTest extends TestCase
{
    public function testArrayToXml()
    {
        $arr = [];
        $xml = "";
        $res = XmlHelper::arrayToXml($arr);
        $this->assertTrue(is_string($res));
        $this->assertSame($xml, $res);

        $arr = ['a' => 'aaa', 'b' => ['c' => '1234', 'd' => ""]];
        $xml = "<a><![CDATA[aaa]]></a><b><c>1234</c><d><![CDATA[]]></d></b>";
        $res = XmlHelper::arrayToXml($arr);
        $this->assertTrue(is_string($res));
        $this->assertSame($xml, $res);
    }

    public function testXmlToArray()
    {
        $xml = "";
        $res = XmlHelper::xmlToArray($xml);
        $this->assertEquals(0, count($res));

        $arr = [
            "note" => [
                "to"      => "Tove",
                "form"    => "Jani",
                "heading" => "Reminder",
                "body"    => "Don't forget me this weekend!"
            ]
        ];
        $xml = "<xml><note><to>Tove</to><from>Jani</from><heading>Reminder</heading><body>Don't forget me this weekend!</body></note></xml>";
        $res = XmlHelper::xmlToArray($xml);
        $this->assertTrue(isset($res['note']));
        $this->assertEquals(count($arr['note']), count($res['note']));
        $this->assertSame("Tove", $res['note']['to']);
    }

    public function testXmlExample2()
    {
        $xml = '<xml><ToUserName><![CDATA[gh_43428a37b583]]></ToUserName>
<FromUserName><![CDATA[ou41P1fSXSdAkgzqgbfIITaTE9SY]]></FromUserName>
<CreateTime>1564527768</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
<EventKey><![CDATA[]]></EventKey>
</xml>';

        $res    = XmlHelper::xmlToArray($xml);
        $expect = [
            'ToUserName'   => 'gh_43428a37b583',
            'FromUserName' => 'ou41P1fSXSdAkgzqgbfIITaTE9SY',
            'CreateTime'   => '1564527768',
            'MsgType'      => 'event',
            'Event'        => 'subscribe',
            'EventKey'     => [],
        ];

        $this->assertEquals($res, $expect);
    }
}
