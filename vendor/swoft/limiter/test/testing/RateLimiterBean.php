<?php declare(strict_types=1);


namespace SwoftTest\Limiter\Testing;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Limiter\Annotation\Mapping\RateLimiter;

/**
 * Class RateLimiterBean
 *
 * @since 2.0
 *
 * @Bean()
 */
class RateLimiterBean
{
    /**
     * @RateLimiter(rate=3, default=3, max=3)
     *
     * @return string
     */
    public function limitByCount(): string
    {
        return 'limitByCount';
    }

    /**
     * @RateLimiter(rate=1, default=1, max=1, key="name~':'~uid")
     *
     * @param string $name
     * @param int    $uid
     *
     * @return string
     */
    public function limitByEl(string $name, int $uid): string
    {
        return sprintf('%s-%d', $name, $uid);
    }

    /**
     * @RateLimiter(key="name~':'~key.getKey(id, 'kname')")
     *
     * @param KeyHelper $key
     * @param int       $id
     * @param string    $name
     *
     * @return string
     */
    public function limitByElObj(KeyHelper $key, int $id, string $name): string
    {
        return sprintf('limitByElObj-%d', $id);
    }

    /**
     * @RateLimiter(rate=1, default=1, max=1, fallback="limitByFallback")
     *
     * @return string
     */
    public function limitByFall(): string
    {
        return 'limitByFall';
    }

    /**
     * @return string
     */
    public function limitByFallback(): string
    {
        return 'limitByFallback';
    }

    /**
     * @RateLimiter(key="CLASS~':'~METHOD")
     *
     * @return string
     */
    public function limitInnerVars(): string
    {
        return 'limitInnerVars';
    }
}