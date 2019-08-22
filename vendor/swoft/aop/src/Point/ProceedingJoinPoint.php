<?php declare(strict_types=1);


namespace Swoft\Aop\Point;

use Swoft\Aop\Contract\ProceedingJoinPointInterface;
use Throwable;

/**
 * Class ProceedingJoinPoint
 *
 * @since 2.0
 */
class ProceedingJoinPoint extends JoinPoint implements ProceedingJoinPointInterface
{
    /**
     * proceed
     *
     * @param array $params
     * If the params is not empty, the params is used to call the method of target
     *
     * @return mixed
     * @throws Throwable
     */
    public function proceed($params = [])
    {
        return $this->handler->invokeTarget($params);
    }

    public function reProceed(array $args = [])
    {
    }
}