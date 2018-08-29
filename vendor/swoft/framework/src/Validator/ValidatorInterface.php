<?php

namespace Swoft\Validator;

/**
 * Interface ValidatorInterface
 *
 * @package Swoft\Validator
 */
interface ValidatorInterface
{
    /**
     * @param array ...$params
     * @return mixed
     */
    public function validate(...$params);
}
