<?php declare(strict_types=1);


namespace Swoft\Redis;

/**
 * Class RedisEvent
 *
 * @since 2.0
 */
class RedisEvent
{
    /**
     * Before command
     */
    const BEFORE_COMMAND = 'swoft.redis.command.before';

    /**
     * After command
     */
    const AFTER_COMMAND = 'swoft.redis.command.after';
}