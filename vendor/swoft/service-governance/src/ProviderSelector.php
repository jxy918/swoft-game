<?php

namespace Swoft\Sg;

use Swoft\App;
use Swoft\Exception\InvalidArgumentException;
use Swoft\Sg\Provider\ConsulProvider;
use Swoft\Sg\Provider\ProviderInterface;

/**
 * Provider selector
 */
class ProviderSelector implements SelectorInterface
{
    /**
     * consul
     */
    const TYPE_CONSUL = 'consul';

    /**
     * Default provider
     *
     * @var string
     */
    private $provider = self::TYPE_CONSUL;

    /**
     * @var array
     */
    private $providers
        = [

        ];

    /**
     * Select a provider by Selector
     *
     * @param string $type
     * @return ProviderInterface
     * @throws \Swoft\Exception\InvalidArgumentException
     */
    public function select(string $type = null)
    {
        if (empty($type)) {
            $type = $this->provider;
        }

        $providers = $this->mergeProviders();
        if (!isset($providers[$type])) {
            throw new InvalidArgumentException(sprintf('Provider %s does not exist', $type));
        }

        $providerBeanName = $providers[$type];

        return App::getBean($providerBeanName);
    }

    /**
     * merge default and config packers
     *
     * @return array
     */
    private function mergeProviders()
    {
        return array_merge($this->providers, $this->defaultProvivers());
    }

    /**
     * the balancers of default
     *
     * @return array
     */
    private function defaultProvivers()
    {
        return [
            self::TYPE_CONSUL => ConsulProvider::class,
        ];
    }
}
