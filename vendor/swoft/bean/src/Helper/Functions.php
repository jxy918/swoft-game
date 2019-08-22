<?php declare(strict_types=1);

use Swoft\Bean\BeanFactory;
use Swoft\Bean\Container;
use Swoft\Bean\Exception\ContainerException;

if (!function_exists('bean')) {
    /**
     * Get bean by name
     *
     * @param string $name Bean name Or alias Or class name
     *
     * @return object|string|mixed
     */
    function bean(string $name)
    {
        if (BeanFactory::isSingleton('config')) {
            return BeanFactory::getBean($name);
        }

        return sprintf('${%s}', $name);
    }
}

if (!function_exists('container')) {
    /**
     * Get container
     *
     * @return Container
     */
    function container(): Container
    {
        return Container::getInstance();
    }
}
