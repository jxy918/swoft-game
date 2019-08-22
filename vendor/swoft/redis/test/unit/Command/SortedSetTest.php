<?php declare(strict_types=1);


namespace SwoftTest\Redis\Unit\Command;


use Swoft\Redis\Redis;
use SwoftTest\Redis\Unit\TestCase;

/**
 * Class SortedSetTest
 *
 * @since 2.0
 */
class SortedSetTest extends TestCase
{
    public function testZadd()
    {
        $key    = \uniqid();
        $scores = [
            'key1' => 11,
            'key3' => 11,
            'key4' => 11,
            'key2' => 21,
        ];

        // zAdd 12 key1 14 key3 16 key4
        $result = Redis::zAdd($key, $scores);

        $this->assertEquals($result, \count($scores));

    }

    public function testKeyScoresAdd()
    {

        $key    = __FUNCTION__;
        Redis::del($key);

        $scores1 = [
            'key1' => 11,
            'key3' => 11,
            'key4' => 11,
            'key2' => 21,
        ];

        // zAdd 12.1 key1 12.3 key2
        $result1 = Redis::zAdd($key, $scores1);

        $this->assertEquals($result1, count($scores1));
    }

    public function testZrem()
    {
        $key    = __FUNCTION__;
        Redis::del($key);

        $scores = [
            'key121' => 3,
            'key22'  => 4,
            'key33'  => 5,
        ];

        Redis::zAdd($key, $scores);
        $res = Redis::zRem($key,  'key22');
        $this->assertEquals(1, $res);
    }

    public function testRange()
    {
        $key = __FUNCTION__ . 'hah';
        Redis::del($key);
        // Floating point is not supported Automatic conversion integer
        $scores = [
            'key121' => 3,
            'key22'  => 4,
            'key33'  => 5,
        ];

        Redis::zAdd($key, $scores);
        // return  value=>key
        // command=  ZRANGEBYSCORE $key -inf +inf WITHSCORES
        $res = Redis::zRangeByScore($key, '-inf', '+inf', ['withscores' => true]);
        // command=  ZRANGEBYSCORE $key 12 17 WITHSCORES
        $res1 = Redis::zRangeByScore($key, '1', '17', ['withscores' => true, 'limit' => [0, 3]]);
        $res2 = Redis::zRangeByScore($key, '-inf', '17', ['withscores' => true]);
        $res3 = Redis::zRangeByScore($key, '1', '+inf', ['withscores' => true]);

        $this->assertEquals($res, $res1);
        $this->assertEquals($res, $res2);
        $this->assertEquals($res, $res3);

        $this->assertEquals(\count($scores), \count($res));

        foreach ($res as $key => $v) {
            $this->assertTrue(in_array($key, array_keys($scores)));
            $this->assertTrue(in_array($v, $scores));
        }
    }
}
