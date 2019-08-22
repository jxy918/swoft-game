<?php declare(strict_types=1);


namespace Swoft\Validator\Rule;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Ip;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;
use Swoft\Validator\Helper\ValidatorHelper;

/**
 * Class IpRule
 *
 * @since 2.0
 *
 * @Bean(Ip::class)
 */
class IpRule implements RuleInterface
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
        $value = $data[$propertyName];
        if (ValidatorHelper::validateIp($value)) {
            return $data;
        }

        /* @var Ip $item */
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s is invalid ip', $propertyName) : $message;

        throw new ValidatorException($message);
    }
}