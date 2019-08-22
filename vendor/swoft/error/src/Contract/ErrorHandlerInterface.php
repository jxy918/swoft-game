<?php declare(strict_types=1);

namespace Swoft\Error\Contract;

/**
 * Class ErrorHandlerInterface
 *
 * @since 2.0
 */
interface ErrorHandlerInterface
{
    /**
     * @return int
     */
    public function getType(): int;
}
