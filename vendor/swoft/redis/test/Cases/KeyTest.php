<?php

namespace SwoftTest\Redis;

use Swoft\Redis\Pool\Config\RedisPoolConfig;

/**
 * KeyTest
 */
class KeyTest extends AbstractTestCase
{

    /**
     * @dataProvider  keysProvider
     *
     * @param array $keys
     */
    public function testDelete(array $keys)
    {
        $result = $this->redis->deleteMultiple($keys);
        $this->assertTrue($result);

        foreach ($keys as $key) {
            $result = $this->redis->get($key, false);
            $this->assertFalse($result);
        }
    }

    /**
     * @dataProvider  keysProvider
     *
     * @param array $keys
     */
    public function testDeleteByCo(array $keys)
    {
        go(function () use ($keys) {
            $this->testDelete($keys);
        });
    }

    public function testKeys()
    {
        $result = $this->redis->getKeys('*');

        /* @var \Swoft\Redis\Pool\Config\RedisPoolConfig $redisConfig */
        $redisConfig = \bean(RedisPoolConfig::class);
        $prefix      = $redisConfig->getPrefix();
        foreach ($result as $key) {
            $this->assertStringStartsWith($prefix, $key);
        }
    }

    public function testKeysByCo()
    {
        go(function () {
            $this->testKeys();
        });
    }

    /**
     * @dataProvider keysProvider
     *
     * @param array $keys
     */
    public function testHas(array $keys)
    {
        foreach ($keys as $key) {
            $result = $this->redis->has($key);
            $this->assertTrue($result);
        }

        $result = $this->redis->deleteMultiple($keys);
        $this->assertTrue($result);

        foreach ($keys as $key) {
            $result = $this->redis->has($key);
            $this->assertFalse($result);
        }
    }
}