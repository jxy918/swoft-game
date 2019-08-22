<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Confirm;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class AlphaRule
 *
 * @since 2.0
 *
 * @Bean(Confirm::class)
 */
class ConfirmRule implements RuleInterface
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
        /* @var Confirm $item */
        $name = $data[$item->getName()] ?? '';
        $value = $data[$propertyName];

        if ((string)$value === (string)$name) {
            return $data;
        }

        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must be equal %s field', $propertyName, $item->getName()) : $message;

        throw new ValidatorException($message);
    }

}
