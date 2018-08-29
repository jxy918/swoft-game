<?php

namespace SwoftTest\Redis;

use Swoft\App;
use SwoftTest\Redis\Pool\RedisEnvPoolConfig;
use SwoftTest\Redis\Pool\RedisPptPoolConfig;

/**
 * PoolTest
 */
class PoolTest extends AbstractTestCase
{
    public function testRedisPoolPpt()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(RedisPptPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'redis');
        $this->assertEquals($pConfig->getUri(), [
            'tcp://127.0.0.1:6379',
            'tcp://127.0.0.1:6379',
        ]);
        $this->assertEquals($pConfig->getMaxActive(), 8);
        $this->assertEquals($pConfig->getMaxWait(), 8);
        $this->assertEquals($pConfig->getMaxWaitTime(), 3);
        $this->assertEquals($pConfig->getMaxIdleTime(), 60);
        $this->assertEquals($pConfig->getTimeout(), 8);
    }

    public function testRedisPoolEnv()
    {
        /* @var \Swoft\Pool\PoolProperties $pConfig */
        $pConfig = App::getBean(RedisEnvPoolConfig::class);
        $this->assertEquals($pConfig->getName(), 'redis');
        $this->assertEquals($pConfig->getProvider(), 'consul');
        $this->assertEquals($pConfig->getTimeout(), 3);
        $this->assertEquals($pConfig->getUri(), [
            'tcp://127.0.0.1:6379',
            'tcp://127.0.0.1:6379',
        ]);
        $this->assertEquals($pConfig->getBalancer(), 'random');
        $this->assertEquals($pConfig->getMaxActive(), 10);
        $this->assertEquals($pConfig->getMaxWait(), 20);
        $this->assertEquals($pConfig->getMaxWaitTime(), 3);
        $this->assertEquals($pConfig->getMaxIdleTime(), 60);
        $this->assertEquals($pConfig->getTimeout(), 3);
    }
}