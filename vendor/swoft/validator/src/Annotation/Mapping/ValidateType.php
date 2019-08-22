<?php declare(strict_types=1);


namespace Swoft\Validator\Annotation\Mapping;

/**
 * Class ValidateType
 *
 * @since 2.0
 */
class ValidateType
{
    /**
     * Get query params
     */
    public  const GET = 'get';

    /**
     * Post form and body
     */
    public const BODY = 'body';
}