<?php

namespace Swoft\Proxy\Handler;

use Swoft\Aop\Aop;

/**
 * Class AopHandler
 *
 * @package Swoft\Proxy\Handler
 */
class AopHandler implements HandlerInterface
{
    /**
     * @var object
     */
    private $target;

    /**
     * AopHandler constructor.
     *
     * @param object $target
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * ProceedingJoinPoint
     *
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function invoke($method, $parameters)
    {
        /* @var Aop $aop */
        $aop = \bean(Aop::class);

        return $aop->execute($this->target, $method, $parameters);
    }
}
