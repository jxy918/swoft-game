<?php declare(strict_types=1);


namespace SwoftTest\Validator\Testing\Validator;

use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class UserBaseValidate
 *
 * @since 2.0
 *
 * @Validator()
 */
class UserBaseValidate
{
    /**
     * @IsInt()
     *
     * @var int
     */
    protected $start;

    /**
     * @IsInt()
     *
     * @var int
     */
    protected $end;
}