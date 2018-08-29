<?php

namespace SwoftTest\Redis;

use PHPUnit\Framework\TestCase;
use Swoft\Redis\Pool\Config\RedisPoolConfig;
use Swoft\Redis\Redis;

/**
 * AbstractTestCase
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * @var \Swoft\Redis\Redis
     */
    protected $redis;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->redis = bean(Redis::class);
        parent::__construct($name, $data, $dataName);
    }


    /**
     * Tear down
     */
    protected function tearDown()
    {
        parent::tearDown();
        swoole_timer_after(6 * 1000, function () {
            swoole_event_exit();
        });
    }

    public function keysProvider()
    {
        $key  = uniqid();
        $key2 = uniqid();

        $this->redis->set($key, uniqid());
        $this->redis->set($key2, uniqid());

        return [
            [[$key, $key2]],
        ];
    }

    /**
     * @param string $key
     */
    protected function assertPrefix(string $key)
    {
        $result = $this->redis->getKeys(sprintf('*%s', $key));

        /* @var \Swoft\Redis\Pool\Config\RedisPoolConfig $redisConfig */
        $redisConfig = \bean(RedisPoolConfig::class);
        $prefix      = $redisConfig->getPrefix();
        foreach ($result as $key) {
            $this->assertStringStartsWith($prefix, $key);
        }
    }

}