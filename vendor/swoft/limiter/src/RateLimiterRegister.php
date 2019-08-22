<?php declare(strict_types=1);


namespace Swoft\Limiter;


use Swoft\Limiter\Annotation\Mapping\RateLimiter;
use Swoft\Limiter\Exception\RateLImiterException;

class RateLimiterRegister
{
    /**
     * @var array
     *
     * @example
     * [
     *     'className' => [
     *         'method' => [
     *              'name' => '',
     *              'max' => '',
     *              'min' => '',
     *         ],
     *     ]
     * ]
     */
    private static $rateLimiters = [];

    /**
     * @param string      $className
     * @param string      $method
     * @param RateLimiter $rateLimiter
     *
     * @throws RateLImiterException
     */
    public static function registerRateLimiter(string $className, string $method, RateLimiter $rateLimiter): void
    {
        if (isset(self::$rateLimiters[$className][$method])) {
            throw new RateLImiterException(
                sprintf('`@RateLImiter` must be only one on method(%s->%s)!', $className, $method)
            );
        }

        $rlConfig = [];
        $config   = $rateLimiter->getConfig();
        foreach ($config as $key) {
            $configMethod   = sprintf('get%s', ucfirst($key));
            $rlConfig[$key] = $rateLimiter->{$configMethod}();
        }

        self::$rateLimiters[$className][$method] = $rlConfig;
    }

    /**
     * @param string $className
     * @param string $method
     *
     * @return array
     */
    public static function getRateLimiter(string $className, string $method): array
    {
        return self::$rateLimiters[$className][$method];
    }
}