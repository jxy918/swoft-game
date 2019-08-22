<?php declare(strict_types=1);


namespace Swoft\Validator\Contract;

/**
 * Class RuleInterface
 *
 * @since 2.0
 */
interface RuleInterface
{
    /**
     * @param array  $data
     * @param string $propertyName
     * @param object $item
     * @param mixed  $default
     *
     * @return array
     */
    public function validate(array $data, string $propertyName, $item, $default = null): array;
}