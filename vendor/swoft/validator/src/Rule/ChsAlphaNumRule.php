<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\ChsAlphaNum;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class ChsAlphaNumRule
 *
 * @since 2.0
 *
 * @Bean(ChsAlphaNum::class)
 */
class ChsAlphaNumRule implements RuleInterface
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
        $rule = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u';
        if (preg_match($rule, $value)) {
            return $data;
        }

        /* @var ChsAlphaNum $item */
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must be Chinese characters  alpha or number',
            $propertyName) : $message;

        throw new ValidatorException($message);
    }
}
