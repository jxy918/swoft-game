<?php declare(strict_types=1);


namespace Swoft\Limiter\Aspect;


use Swoft\Aop\Annotation\Mapping\Around;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\PointAnnotation;
use Swoft\Aop\Point\ProceedingJoinPoint;
use Swoft\Aop\Proxy;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Limiter\RateLimter;
use Throwable;
use Swoft\Limiter\Annotation\Mapping\RateLimiter;

/**
 * Class LimiterAspect
 *
 * @since 2.0
 *
 * @Aspect()
 * @PointAnnotation(
 *     include={RateLimiter::class}
 * )
 */
class LimiterAspect
{
    /**
     * @Inject("rateLimiter")
     *
     * @var RateLimter
     */
    private $rateLimiter;

    /**
     * @Around()
     *
     * @param ProceedingJoinPoint $proceedingJoinPoint
     *
     * @return mixed
     * @throws Throwable
     */
    public function around(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $args      = $proceedingJoinPoint->getArgs();
        $target    = $proceedingJoinPoint->getTarget();
        $method    = $proceedingJoinPoint->getMethod();
        $className = get_class($target);
        $className = Proxy::getOriginalClassName($className);

        $result = $this->rateLimiter->checkRate([$proceedingJoinPoint, 'proceed'], $className, $method, $target, $args);
        return $result;
    }
}