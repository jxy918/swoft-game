<?php declare(strict_types=1);


namespace Swoft\Breaker\Aspect;

use Swoft\Aop\Annotation\Mapping\Around;
use Swoft\Aop\Annotation\Mapping\Aspect;
use Swoft\Aop\Annotation\Mapping\PointAnnotation;
use Swoft\Aop\Point\ProceedingJoinPoint;
use Swoft\Aop\Proxy;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Breaker\Annotation\Mapping\Breaker;
use Swoft\Breaker\BreakerManager;
use Throwable;

/**
 * Class BreakerAspect
 *
 * @since 2.0
 *
 * @Aspect()
 * @PointAnnotation(
 *     include={Breaker::class}
 * )
 */
class BreakerAspect
{
    /**
     * @Inject()
     *
     * @var BreakerManager
     */
    private $breakerManager;

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

        $breaker = $this->breakerManager->getBreaker($className, $method);
        $result  = $breaker->run($target, $className, $method, [$proceedingJoinPoint, 'proceed'], $args);
        return $result;
    }
}