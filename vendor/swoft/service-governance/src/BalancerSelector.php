<?php

namespace Swoft\Sg;

use Swoft\App;
use Swoft\Exception\InvalidArgumentException;
use Swoft\Sg\Balancer\BalancerInterface;
use Swoft\Sg\Balancer\RandomBalancer;

/**
 * the manager of balancer
 */
class BalancerSelector implements SelectorInterface
{
    /**
     * the name of random
     */
    const TYPE_RANDOM = 'random';

    /**
     * @var string
     */
    private $balancer = self::TYPE_RANDOM;

    /**
     * @var array
     */
    private $balancers = [

    ];

    /**
     * get balancer
     *
     * @param string $type
     *
     * @return BalancerInterface
     */
    public function select(string $type = null)
    {
        if (empty($type)) {
            $type = $this->balancer;
        }
        $balancers = $this->mergeBalancers();
        if (!isset($balancers[$type])) {
            throw new InvalidArgumentException(sprintf('Balancer %s does not exist', $type));
        }

        $balancerBeanName = $balancers[$type];

        return App::getBean($balancerBeanName);
    }

    /**
     * merge default and config packers
     *
     * @return array
     */
    private function mergeBalancers()
    {
        return array_merge($this->balancers, $this->defaultBalancers());
    }

    /**
     * the balancers of default
     *
     * @return array
     */
    private function defaultBalancers()
    {
        return [
            self::TYPE_RANDOM => RandomBalancer::class,
        ];
    }
}
