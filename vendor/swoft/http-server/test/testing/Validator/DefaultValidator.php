<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Testing\Validator;

use Swoft\Validator\Annotation\Mapping\IsArray;
use Swoft\Validator\Annotation\Mapping\IsBool;
use Swoft\Validator\Annotation\Mapping\IsFloat;
use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class DefaultValidator
 *
 * @since 2.0
 *
 * @Validator()
 */
class DefaultValidator
{
    /**
     * @IsString()
     *
     * @var string
     */
    protected $string = 'string';

    /**
     * @IsInt()
     *
     * @var int
     */
    protected $int = 1;

    /**
     * @IsFloat()
     *
     * @var float
     */
    protected $float = 1.2;

    /**
     * @IsBool()
     *
     * @var bool
     */
    protected $bool = true;

    /**
     * @IsArray()
     *
     * @var array
     */
    protected $array = ['array'];
}