<?php

namespace Swoft\Redis\Aspect;

use Swoft\Aop\JoinPoint;
use Swoft\Bean\Annotation\AfterReturning;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\Before;
use Swoft\Bean\Annotation\PointBean;
use Swoft\Log\Log;

/**
 * the aspect of redis
 *
 * @Aspect()
 * @PointBean({
 *     RedisCache::class
 * })
 *
 * @uses      RedisAspect
 * @version   2018年01月03日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RedisAspect
{
    /**
     * the prefix of profile
     */
    const PROFILE_PREFIX = 'redis';

    /**
     * before of method
     *
     * @Before()
     * @param JoinPoint $joinPoint
     */
    public function before(JoinPoint $joinPoint)
    {
        $profileKey = $this->getProfileKey($joinPoint);
        Log::profileStart($profileKey);
    }

    /**
     * afterReturning of method
     *
     * @AfterReturning()
     * @param JoinPoint $joinPoint
     *
     * @return mixed
     */
    public function afterReturning(JoinPoint $joinPoint)
    {
        $profileKey = $this->getProfileKey($joinPoint);
        Log::profileEnd($profileKey);

        return $joinPoint->getReturn();
    }

    /**
     * the key of profile
     *
     * @param JoinPoint $joinPoint
     *
     * @return string
     */
    private function getProfileKey(JoinPoint $joinPoint)
    {
        $method = $joinPoint->getMethod();
        if ($method == '__call') {
            list($method) = $joinPoint->getArgs();
        }

        return self::PROFILE_PREFIX . '.' . $method;
    }
}
