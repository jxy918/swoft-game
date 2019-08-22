<?php declare(strict_types=1);

namespace Swoft\Validator\Rule;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Url;
use Swoft\Validator\Contract\RuleInterface;
use Swoft\Validator\Exception\ValidatorException;

/**
 * Class UrlRule
 *
 * @since 2.0
 *
 * @Bean(Url::class)
 */
class UrlRule implements RuleInterface
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
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $data;
        }

        /* @var Url $item */
        $message = $item->getMessage();
        $message = (empty($message)) ? sprintf('%s must be a url', $propertyName) : $message;

        throw new ValidatorException($message);
    }
}
