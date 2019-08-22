<?php declare(strict_types=1);


namespace Swoft\Consul\Helper;

/**
 * Class OptionsResolver
 *
 * @since 2.0
 */
class OptionsResolver
{
    /**
     * @param array $options
     * @param array $availableOptions
     *
     * @return array
     */
    public static function resolve(array $options, array $availableOptions): array
    {
        return array_intersect_key($options, array_flip($availableOptions));
    }
}