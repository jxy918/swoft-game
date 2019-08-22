<?php declare(strict_types=1);


namespace Swoft\Validator\Contract;

/**
 * Class ValidatorInterface
 *
 * @since 2.0
 */
interface ValidatorInterface
{
    /**
     * Validate error is thrown exception, otherwise is return `$data`
     *
     * @param array  $data
     * @param array  $params
     *
     * @return array
     */
    public function validate(array $data, array $params): array ;
}