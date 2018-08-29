<?php

namespace SwoftTest\Redis;

use Swoft\App;

/**
 * StringTest
 */
class StringTest extends AbstractTestCase
{
    public function testSet()
    {
        $value = uniqid();
        $key   = 'stringKey';
        if (App::isCoContext()) {
            $key .= 'co';
        }

        $nottlResult = $this->redis->set($key . 'key2', uniqid());
        $this->assertTrue($nottlResult);

        $result = $this->redis->set($key, $value, 100);
        $this->assertTrue($result);

        $ttl = $this->redis->ttl($key);
        $this->assertGreaterThan(1, $ttl);

        $getValue = $this->redis->get($key);
        $this->assertEquals($getValue, $value);
    }

    public function testSetByCo()
    {
        go(function () {
            $this->testSet();
        });
    }

    public function testGet()
    {
        $default = 'defualtValue';
        $result  = $this->redis->get("notKey" . uniqid(), $default);
        $this->assertSame($result, $default);
    }

    public function testGetByCo()
    {
        go(function () {
            $this->testGet();
        });
    }

    public function testMsetAndMget()
    {
        $key    = uniqid();
        $key2   = uniqid();
        $value  = 'value1';
        $value2 = 'val2';

        $result = $this->redis->mset([
            $key  => $value,
            $key2 => $value2,
        ]);

        $this->assertTrue($result);

        $values = $this->redis->mget([$key2, $key]);
        $this->assertEquals($values[$key], $value);
        $this->assertEquals($values[$key2], $value2);
    }

    public function testMsetAndMgetByCo()
    {
        go(function () {
            $this->testMsetAndMget();
        });
    }
}