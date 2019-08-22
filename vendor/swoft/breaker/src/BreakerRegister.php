<?php declare(strict_types=1);


namespace Swoft\Breaker;

use Swoft\Breaker\Annotation\Mapping\Breaker as BreakerAnnotation;
use Swoft\Breaker\Exception\BreakerException;

/**
 * Class BreakerRegister
 *
 * @since 2.0
 */
class BreakerRegister
{
    /**
     * @var array
     *
     * @example
     * [
     *     'className' => [
     *         'methodName' => new BreakerAnnotation(),
     *         'methodName' => new BreakerAnnotation(),
     *         'methodName' => new BreakerAnnotation(),
     *     ]
     * ]
     */
    private static $breakers = [];

    /**
     * @param string            $className
     * @param string            $method
     * @param BreakerAnnotation $breaker
     *
     * @throws BreakerException
     */
    public static function registerBreaker(string $className, string $method, BreakerAnnotation $breaker)
    {
        if (isset(self::$breakers[$className][$method])) {
            throw new BreakerException(
                sprintf('`@Breaker` must be only one on method(%s->%s)!', $className, $method)
            );
        }

        self::$breakers[$className][$method] = $breaker;
    }

    /**
     * @return array
     */
    public static function getBreakers(): array
    {
        return self::$breakers;
    }
}