<?php declare(strict_types=1);


namespace SwoftTest\Redis\Unit\Command;


use Swoft\Redis\Redis;
use SwoftTest\Redis\Unit\TestCase;

/**
 * Class HashTest
 *
 * @since 2.0
 */
class HashTest extends TestCase
{

    public function testhMsetAndhMget()
    {
        $key    = \uniqid();
        $values = [
            'key'  => [\uniqid()],
            'key2' => new self(),
        ];

        $result = Redis::hMSet($key, $values);
        $this->assertTrue($result);

        $getKeys   = array_keys($values);
        $getKeys[] = 'key3';

        $resultValues = Redis::hMGet($key, $getKeys);

        $this->assertEquals(\count($resultValues), 2);
        $this->assertEquals($resultValues, $values);
    }
}
