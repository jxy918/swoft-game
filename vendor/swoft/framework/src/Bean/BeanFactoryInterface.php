<?php

namespace Swoft\Bean;

/**
 * Bean factory interface
 */
interface BeanFactoryInterface
{
    /**
     * Get bean
     *
     * @param string $name
     * @return mixed
     */
    public static function getBean(string $name);

    /**
     * @param string $name
     * @return bool
     */
    public static function hasBean(string $name): bool;
}
