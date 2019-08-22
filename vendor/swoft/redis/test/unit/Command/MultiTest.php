<?php declare(strict_types=1);


namespace SwoftTest\Redis\Unit\Command;


use Swoft\Redis\Redis;
use SwoftTest\Redis\Unit\TestCase;

/**
 * Class MultiTest
 *
 * @since 2.0
 */
class MultiTest extends TestCase
{
    public function testPipeline()
    {
        $count  = 10;
        $result = Redis::pipeline(function (\Redis $redis) use ($count) {
            for ($i = 0; $i < $count; $i++) {
                $redis->set("key:$i", $i);
            }
        });

        $this->assertEquals(\count($result), $count);

        foreach ($result as $index => $value) {
            $this->assertTrue($value);
        }
    }

    public function testTransaction()
    {
        $count  = 2;
        $result = Redis::transaction(function (\Redis $redis) use ($count) {
            for ($i = 0; $i < $count; $i++) {
                $key = "key:$i";
                $redis->set($key, $i);
                $redis->get($key);
            }
        });

        /**
        array(4) {
        [0]=>
        bool(true)
        [1]=>
        int(0)
        [2]=>
        bool(true)
        [3]=>
        int(1)
        }

         */

        $this->assertEquals(\count($result), $count * 2);

        foreach ($result as $index => $value) {
            if ($index % 2 == 0) {
                $this->assertTrue($value);
            } else {
                $this->assertGreaterThan(0, $index + 1);
            }
        }
    }
}
