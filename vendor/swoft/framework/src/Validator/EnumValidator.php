<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\ValidatorHelper;

/**
 * Enum validator
 * @Bean()
 */
class EnumValidator implements ValidatorInterface
{
    /**
     * @param array ...$params
     *
     * @return mixed
     * @throws \Swoft\Exception\ValidatorException
     */
    public function validate(...$params)
    {
        list($name, $value, $validValues, $throws, $template) = $params;

        return ValidatorHelper::validateEnum($name, $value, $validValues, $throws, $template);
    }
}
