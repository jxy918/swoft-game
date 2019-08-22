<?php declare(strict_types=1);


namespace Swoft\Aop\Contract;

/**
 * Class ProceedingJoinPointInterface
 *
 * @since 2.0
 */
interface ProceedingJoinPointInterface
{
    /**
     * @param array $params
     *
     * @return mixed
     */
    public function proceed($params = []);
}