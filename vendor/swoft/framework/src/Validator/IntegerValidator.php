<?php

namespace Swoft\Validator;

use Swoft\Bean\Annotation\Bean;
use Swoft\Helper\ValidatorHelper;

/**
 * Interger validator
 * @Bean()
 */
class IntegerValidator
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

        return ValidatorHelper::validateInteger($name, $value, $min, $max, $throws, $template);
    }
}
