<?php

namespace SwoftTest\Redis;

/**
 * ZsetTest
 */
class ZsetTest extends AbstractTestCase
{
    public function testZadd()
    {
        $key = uniqid();
        $ret = $this->redis->zAdd($key, 1.1, 'key');
        $this->assertEquals($ret, 1);

        $ret2 = $this->redis->zAdd($key, 1.3, 'key2');
        $this->assertEquals($ret2, 1);

        $ret3 = $this->redis->zAdd($key, 3.2, 'key3');
        $this->assertEquals($ret3, 1);

        $ret4 = $this->redis->zAdd($key, 1.2, 'key4');
        $this->assertEquals($ret4, 1);

        $ret5 = $this->redis->zAdd($key, 5.2, 'key5');
        $this->assertEquals($ret5, 1);

        $keys = $this->redis->zRange($key, 0, -1);
        $this->assertCount(5, $keys);

        $data      = [
            'key4',
            'key2',
            'key3',
        ];
        $rangeKeys = $this->redis->zRangeByScore($key, 1.2, 3.2);
        $this->assertEquals($data, $rangeKeys);
    }

    public function testZaddByCo()
    {
        go(function () {
            $this->testZadd();
        });
    }
}