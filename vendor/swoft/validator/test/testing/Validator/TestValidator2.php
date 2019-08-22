<?php declare(strict_types=1);


namespace SwoftTest\Validator\Testing\Validator;

use Swoft\Validator\Annotation\Mapping\Email;
use Swoft\Validator\Annotation\Mapping\Enum;
use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\Ip;
use Swoft\Validator\Annotation\Mapping\Length;
use Swoft\Validator\Annotation\Mapping\Max;
use Swoft\Validator\Annotation\Mapping\Min;
use Swoft\Validator\Annotation\Mapping\Mobile;
use Swoft\Validator\Annotation\Mapping\NotEmpty;
use Swoft\Validator\Annotation\Mapping\Pattern;
use Swoft\Validator\Annotation\Mapping\Range;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class TestValidator2
 *
 * @since 2.0
 *
 * @Validator(name="testDefaultValidator")
 */
class TestValidator2
{
    /**
     * @IsString()
     * @Email(message="email messsage")
     *
     * @var string
     */
    protected $email;

    /**
     * @IsInt()
     * @Enum(values={2,4,6}, message="enum message")
     *
     * @var int
     */
    protected $enum;

    /**
     * @IsString()
     * @Ip(message="ip message")
     *
     * @var string
     */
    protected $ip;

    /**
     * @IsString()
     * @Length(min=2, max=12, message="length message")
     *
     * @var string
     */
    protected $length;

    /**
     * @IsInt()
     * @Max(value=12, message="max message")
     *
     * @var int
     */
    protected $max;

    /**
     * @IsInt()
     * @Min(value=1, message="min message")
     *
     * @var int
     */
    protected $min;

    /**
     * @IsString()
     * @Mobile(message="mobile message")
     *
     * @var string
     */
    protected $mobile;

    /**
     * @IsString()
     * @NotEmpty(message="not empty message")
     *
     * @var string
     */
    protected $notEmpty;

    /**
     * @IsString()
     * @Pattern(regex="*swoft*", message="pattern message")
     *
     * @var string
     */
    protected $pattern;

    /**
     * @IsInt()
     * @Range(min=10, max=99, message="range message")
     *
     * @var int
     */
    protected $range;
}