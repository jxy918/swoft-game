<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\LessThan;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class GreaterThanRule
 *
 * @since 2.0
 *
 * @Bean(LessThan::class)
 */
class LessThanRule implements RuleInterface
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
        /* @var LessThan $item */
        $name = $data[$item->getName()] ?? '';
        $value = $data[$propertyName];
        settype($name, "float");
        settype($value, "float");
        if ($value < $name) {
            return $data;
        }
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must be less than %s field', $propertyName, $item->getName()) : $message;
        throw new ValidatorException($message);
    }
}
