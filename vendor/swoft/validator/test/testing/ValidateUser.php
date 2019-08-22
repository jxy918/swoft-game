<?php declare(strict_types=1);


namespace SwoftTest\Validator\Testing;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Validator\Annotation\Mapping\Validate;
use SwoftTest\Validator\Testing\Validator\UserBaseValidate;
use SwoftTest\Validator\Testing\Validator\UserValidator;


/**
 * Class ValidateUser
 *
 * @since 2.0
 *
 * @Bean()
 */
class ValidateUser
{
    /**
     * @Validate(validator=UserBaseValidate::class)
     * @Validate(validator=UserValidator::class, params={1, "name"})
     */
    public function testUser()
    {

    }
}