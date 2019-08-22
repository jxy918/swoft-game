<?php declare(strict_types=1);


namespace Swoft\Validator\Rule;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Length;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;
use Swoft\Validator\Helper\ValidatorHelper;

/**
 * Class LengthRule
 *
 * @since 2.0
 *
 * @Bean(Length::class)
 */
class LengthRule implements RuleInterface
{
    /**
     * @param array  $data
     * @param string $propertyName
     * @param object $item
     * @param null   $default
     *
     * @return array
     * @throws ValidatorException
     */
    public function validate(array $data, string $propertyName, $item, $default = null): array
    {
        /* @var Length $item */
        $min = $item->getMin();
        $max = $item->getMax();

        $value = $data[$propertyName];
        if (ValidatorHelper::validatelength($value, $min, $max)) {
            return $data;
        }

        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s is invalid length(min=%d, max=%d)', $propertyName, $min,
            $max) : $message;

        throw new ValidatorException($message);
    }
}