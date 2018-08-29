<?php

namespace SwoftTest\Sg;

use Swoft\Sg\Balancer\RandomBalancer;
use Swoft\Sg\Balancer\RoundRobinBalancer;

/**
 * Test
 */
class BalancerTest extends AbstractTestCase
{

    /**
     * @test
     */
    public function random()
    {
        $list = [1, 2, 3];
        $balancer = new RandomBalancer();
        $value = $balancer->select($list);
        $this->assertTrue(in_array($value, $list));
    }

    /**
     * @test
     */
    public function roundRobin()
    {
        $list = [1, 2, 3];
        $balancer = new RoundRobinBalancer();
        $value = $balancer->select($list);
        $this->assertEquals($list[0], $value);
        $value = $balancer->select($list);
        $this->assertEquals($list[1], $value);
        $value = $balancer->select($list);
        $this->assertEquals($list[2], $value);
        $value = $balancer->select($list);
        $this->assertEquals($list[0], $value);
    }

}