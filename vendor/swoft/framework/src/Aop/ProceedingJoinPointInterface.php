<?php

namespace Swoft\Aop;

/**
 * the proceedingJoinPoint of interface
 *
 * @uses      ProceedingJoinPointInterface
 * @version   2017年12月25日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
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
