<?php

namespace SwoftTest\Redis;

/**
 * HashTest
 */
class HashTest extends AbstractTestCase
{
    public function testHmsetAndHmget()
    {
        $key    = uniqid();
        $result = $this->redis->hMset($key, ['key' => 'value', 'key2' => 'value2', 'key3' => 'value3']);
        $this->assertEquals(true, $result);

        $data   = [
            'key2' => 'value2',
            'key'  => 'value',
        ];
        $values = $this->redis->hMGet($key, ['key2', 'key']);
        $this->assertEquals($data, $values);

        $data   = [
            'NotExistKey'  => false,
            'NotExistKey2' => false,
        ];
        $values = $this->redis->hMGet($key, ['NotExistKey', 'NotExistKey2']);
        $this->assertEquals($data, $values);
    }

    public function testHmsetAndHmgetByCo()
    {
        go(function () {
            $this->testHmsetAndHmget();
        });
    }
}