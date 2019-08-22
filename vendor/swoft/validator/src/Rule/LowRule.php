<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Low;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class LowRule
 *
 * @since 2.0
 *
 * @Bean(Low::class)
 */
class LowRule implements RuleInterface
{
    /**
     * @param array $data
     * @param string $propertyName
     * @param object $item
     * @param null $default
     *
     * @return array
     * @throws ValidatorException
     */
    public function validate(array $data, string $propertyName, $item, $default = null): array
    {
        $value = $data[$propertyName];
        $rule = '/^[a-z]+$/';
        if (preg_match($rule, $value)) {
            return $data;
        }

        /* @var Low $item */
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must be lowercase alpha', $propertyName) : $message;

        throw new ValidatorException($message);
    }
}
