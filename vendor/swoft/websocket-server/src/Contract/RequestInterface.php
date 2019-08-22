<?php declare(strict_types=1);

namespace Swoft\WebSocket\Server\Contract;

/**
 * Interface RequestInterface
 *
 * @since 2.0
 */
interface RequestInterface
{
    /**
     * @return int
     */
    public function getFd(): int;
}
