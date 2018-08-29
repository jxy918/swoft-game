<?php

namespace SwoftTest\Redis;

/**
 * SetTest
 */
class SetTest extends AbstractTestCase
{
    public function testSaddAndSMembers()
    {
        $key    = uniqid();
        $value1 = uniqid();
        $value2 = uniqid();
        $value3 = uniqid();
        $value4 = uniqid();

        $this->redis->sAdd($key, $value1, $value2, $value3);
        $this->redis->sAdd($key, $value4);

        $values = [$value1, $value2, $value3, $value4];

        $members = $this->redis->sMembers($key);
        sort($members);
        sort($values);

        $this->assertEquals($members, $values);

        $this->assertPrefix($key);
    }

    public function testSaddAndSMembersByCo()
    {
        go(function () {
            $this->testSaddAndSMembers();
        });
    }

    public function testSremoveAndScontainsAndScard()
    {
        $key    = uniqid();
        $value1 = uniqid();
        $value2 = uniqid();
        $this->redis->sAdd($key, $value1, $value2);
        $result = $this->redis->sMembers($key);
        $this->assertCount(2, $result);

        $result = $this->redis->sIsMember($key, $value1);
        $this->assertTrue($result);

        $result = $this->redis->sCard($key);
        $this->assertEquals(2, $result);

        $result = $this->redis->sRem($key, $value1);
        $this->assertEquals(1, $result);

        $members = $this->redis->sMembers($key);
        $this->assertCount(1, $members);

        $this->assertPrefix($key);
    }

    public function testSremoveByCo()
    {
        go(function () {
            $this->testSremoveAndScontainsAndScard();
        });
    }

}