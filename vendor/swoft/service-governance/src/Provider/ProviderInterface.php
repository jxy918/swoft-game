<?php

namespace Swoft\Sg\Provider;

/**
 * Provier interface
 */
interface ProviderInterface
{
    /**
     * @param string $serviceName
     * @param array  ...$params
     *
     * @return mixed
     */
    public function getServiceList(string $serviceName, ...$params);

    /**
     * @param array ...$params
     *
     * @return mixed
     */
    public function registerService(...$params);
}
