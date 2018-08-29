<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\ValidatorHelper;

/**
 * Number validator
 * @Bean()
 */
class NumberValidator
{
    /**
     * @param array ...$params
     *
     * @return mixed
     * @throws \Swoft\Exception\ValidatorException
     */
    public function validate(...$params)
    {
        list($name, $value, $min, $max, $throws, $template) = $params;

        return ValidatorHelper::validateNumber($name, $value, $min, $max, $throws, $template);
    }
}
