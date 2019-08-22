<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use DateTime;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\BeforeDate;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class BeforeDateRule
 *
 * @since 2.0
 *
 * @Bean(BeforeDate::class)
 */
class BeforeDateRule implements RuleInterface
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
        /* @var BeforeDate $item */
        $date = $item->getDate();
        $value = $data[$propertyName];
        if (is_string($value)) {
            $dt = DateTime::createFromFormat("Y-m-d H:i:s", $value);
            if (($dt !== false && !array_sum($dt::getLastErrors())) && strtotime($value) <= strtotime($date)) {
                return $data;
            } elseif (ctype_digit($value)) {
                if (date('Y-m-d', (int)$value) && $value <= strtotime($date)) {
                    return $data;
                }
            }
        } elseif (filter_var($value, FILTER_VALIDATE_INT)) {
            if ($value <= strtotime($date)) {
                return $data;
            }
        }
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must be before %s', $propertyName, $date) : $message;
        throw new ValidatorException($message);
    }
}
