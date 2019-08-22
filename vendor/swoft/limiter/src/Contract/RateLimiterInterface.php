<?php declare(strict_types=1);


namespace Swoft\Limiter\Contract;

/**
 * Class RateLimiterInterface
 *
 * @since 2.0
 */
interface RateLimiterInterface
{
    /**
     * @param array $config
     *
     * @return bool
     */
    public function getTicket(array $config): bool;
}