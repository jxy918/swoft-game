<?php

namespace Swoft\Sg\Balancer;

/**
 * the balancer of connect pool
 */
interface BalancerInterface
{
    public function select(array $serviceList, ...$params);
}
