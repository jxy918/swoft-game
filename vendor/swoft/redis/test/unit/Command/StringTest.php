<?php declare(strict_types=1);


namespace SwoftTest\Redis\Unit\Command;


use Swoft\Redis\Redis;
use SwoftTest\Redis\Unit\TestCase;

/**
 * Class StringTest
 *
 * @since 2.0
 */
class StringTest extends TestCase
{
    public function testSet()
    {
        $key    = \uniqid();
        $result = Redis::set($key, \uniqid());
        $this->assertTrue($result);

        $ttl    = 100;
        $ttlKey = \uniqid();
        Redis::set($ttlKey, uniqid(), $ttl);

        $getTtl = Redis::ttl($ttlKey);
        $this->assertGreaterThan($ttl / 2, $getTtl);

        Redis::set($key, json_encode(['a']), 111);
        Redis::get($key);
    }

    public function testGet()
    {
        $value = \uniqid();
        $key   = $this->setKey($value);

        $getValue = Redis::get($key);

        $this->assertEquals($value, $getValue);
    }

    public function testArray()
    {
        $key = \uniqid();

        $setData = [
            'goods' => ['goods_id' => 1, 'goods_name' => 'iPhone xx']
        ];
        Redis::set($key, $setData);

        $this->assertEquals($setData, Redis::get($key));
    }

    public function testInc()
    {
        $key = \uniqid();

        $redis = Redis::connection('redis.inc.pool');

        $this->assertEquals(1, $redis->incrBy($key, 1));
        $redis->set($key, 2);
        $redis->incr($key);
        $this->assertEquals(3, $redis->get($key));
    }

    public function testMsetAndMget()
    {
        $key    = ':mset:' . \uniqid();
        $value  = [\uniqid()];
        $key2   = ':mset:' . \uniqid();
        $value2 = [\uniqid()];

        $keys = [
            $key  => $value,
            $key2 => $value2,
        ];

        $result = Redis::mset($keys);
        $this->assertTrue($result);


        $resultVlue  = Redis::get($key);
        $resultVlue2 = Redis::get($key2);

        $this->assertEquals($value, $resultVlue);
        $this->assertEquals($value2, $resultVlue2);

        $values = Redis::mget([$key, $key2, 'key3']);

        $this->assertEquals(count($values), 2);
        $this->assertEquals($values, $keys);
    }
}
