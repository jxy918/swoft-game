<?php

namespace Swoft\Sg\Balancer;

use Swoft\Bean\Annotation\Bean;

/**
 * 轮询负载
 * @Bean()
 */
class RoundRobinBalancer implements BalancerInterface
{
    private $lastIndex = 0;

    public function select(array $serviceList, ...$params)
    {
        $currentIndex = $this->lastIndex;
        $value = $serviceList[$currentIndex];
        if ($currentIndex + 1 > count($serviceList) - 1) {
            $this->lastIndex = 0;
        } else {
            $this->lastIndex++;
        }
        return $value;
    }
}
