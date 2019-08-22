<?php declare(strict_types=1);


namespace Swoft\Limiter;


use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Limiter\Rate\RedisRateLimiter;
use Swoft\SwoftComponent;

/**
 * Class AutoLoader
 *
 * @since 2.0
 */
class AutoLoader extends SwoftComponent
{
    /**
     * Get namespace and dirs
     *
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * @return array
     */
    public function metadata(): array
    {
        return [];
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function beans(): array
    {
        return [
            'rateLimiter' => [
                'class'      => RateLimter::class,
                'rateLimter' => bean('redisRateLimiter'),
            ]
        ];
    }
}