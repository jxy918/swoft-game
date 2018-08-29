<?php

namespace Swoft\Validator;

use Swoft\App;
use Swoft\Bean\BeanFactory;
use Swoft\Exception\ValidatorException;

/**
 * Class AbstractValidator
 *
 * @package Swoft\Validator
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @param string $name Property name
     * @param mixed  $value Property value
     * @param array  $info  Validator info
     * @throws \Swoft\Exception\ValidatorException
     */
    protected function doValidation(string $name, $value, array $info)
    {
        if (! isset($info['validator']) || ! isset($info['params'])) {
            return;
        }

        $validatorBeanName = $info['validator'];
        if (! BeanFactory::hasBean($validatorBeanName)) {
            throw new ValidatorException(sprintf('Validator %s is not exist', $validatorBeanName));
        }

        /* @var \Swoft\Validator\ValidatorInterface $validator */
        $params = $info['params'];
        array_unshift($params, $name, $value);
        $validator = App::getBean($validatorBeanName);
        $validator->validate(...$params);
    }
}
