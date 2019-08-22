<?php declare(strict_types=1);


namespace SwoftTest\Validator\Testing\Validator;

use Swoft\Validator\Annotation\Mapping\Email;
use Swoft\Validator\Annotation\Mapping\Ip;
use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class TestValidator3
 *
 * @since 2.0
 *
 * @Validator()
 */
class TestValidator3
{
    /**
     * @IsString()
     * @Email()
     *
     * @var string
     */
    private $email;

    /**
     * @IsString()
     * @Ip()
     *
     * @var string
     */
    private $ip;


    /**
     * @IsInt()
     *
     * @var int
     */
    private $count;
}