<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use DateTime;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Date;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class IsDateRule
 *
 * @since 2.0
 *
 * @Bean(Date::class)
 */
class DateRule implements RuleInterface
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
        if (is_string($value)) {
            $dt = DateTime::createFromFormat("Y-m-d H:i:s", $value);
            if ($dt !== false && !array_sum($dt::getLastErrors())) {
                return $data;
            } elseif (ctype_digit($value)) {
                if (date('Y-m-d', (int)$value)) {
                    return $data;
                }
            }
        } elseif (filter_var($value, FILTER_VALIDATE_INT)) {
            if ($value >= PHP_INT_MIN && $value <= PHP_INT_MAX) {
                return $data;
            }
        }
        /* @var Date $item */
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must date!', $propertyName) : $message;
        throw new ValidatorException($message);
    }
}
