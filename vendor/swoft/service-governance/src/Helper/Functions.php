<?php
use Swoft\App;
use Swoft\Sg\Circuit\CircuitBreaker;
use Swoft\Sg\Bean\Collector\BreakerCollector;
use Swoft\Sg\BalancerSelector;
use Swoft\Sg\ProviderSelector;

if (!function_exists('breaker')) {
    /**
     * @param string $name
     *
     * @return \Swoft\Sg\Circuit\CircuitBreaker
     */
    function breaker(string $name): CircuitBreaker
    {
        $collector = BreakerCollector::getCollector();
        if (!isset($collector[$name])) {
            throw new InvalidArgumentException("the breaker of $name is not exist!");
        }
        $breakerBeanName = $collector[$name];

        return App::getBean($breakerBeanName);
    }
}

if (!function_exists('fallback')) {
    /**
     * @param string $name
     *
     * @return object
     */
    function fallback(string $name)
    {
        $collector = \Swoft\Sg\Bean\Collector\FallbackCollector::getCollector();
        if (!isset($collector[$name])) {
            return null;
        }

        $beanName = $collector[$name];

        return App::getBean($beanName);
    }
}

if (!function_exists('balancer')) {
    /**
     * @return \Swoft\Sg\BalancerSelector
     */
    function balancer(): BalancerSelector
    {
        return App::getBean('balancerSelector');
    }
}

if (!function_exists('provider')) {
    /**
     * @return \Swoft\Sg\ProviderSelector
     */
    function provider(): ProviderSelector
    {
        return App::getBean('providerSelector');
    }
}



