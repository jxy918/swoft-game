<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\ChsAlphaDash;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class ChsAlphaNumRule
 *
 * @since 2.0
 *
 * @Bean(ChsAlphaDash::class)
 */
class ChsAlphaDashRule implements RuleInterface
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
        $rule = '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-\_]+$/u';
        if (preg_match($rule, $value)) {
            return $data;
        }

        /* @var ChsAlphaDash $item */
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must be Chinese characters  alpha number or dash',
            $propertyName) : $message;

        throw new ValidatorException($message);
    }
}
