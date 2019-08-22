<?php declare(strict_types=1);


namespace Swoft\Validator\Rule;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class IsStringRule
 *
 * @since 2.0
 *
 * @Bean(IsString::class)
 */
class IsStringRule implements RuleInterface
{
    /**
     * @param array      $data
     * @param string     $propertyName
     * @param object     $item
     * @param mixed|null $default
     *
     * @return array
     * @throws ValidatorException
     */
    public function validate(array $data, string $propertyName, $item, $default = null): array
    {
        /* @var IsString $item */
        $message = $item->getMessage();
        if (!isset($data[$propertyName]) && $default !== null) {
            $data[$propertyName] = (string)$default;
            return $data;
        }

        if (!isset($data[$propertyName]) && $default === null) {
            $message = (empty($message)) ? sprintf('%s must exist!', $propertyName) : $message;
            throw new ValidatorException($message);
        }

        $value = $data[$propertyName];
        if (is_string($value)) {
            return $data;
        }

        $message = (empty($message)) ? sprintf('%s must string!', $propertyName) : $message;
        throw new ValidatorException($message);
    }
}