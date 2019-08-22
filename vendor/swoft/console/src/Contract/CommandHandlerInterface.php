<?php declare(strict_types=1);

namespace Swoft\Console\Contract;

/**
 * Interface CommandHandlerInterface
 * - Use for implement a independent command handler
 * @since 2.0
 */
interface CommandHandlerInterface
{
    /**
     * @return mixed
     */
    public function execute();
}
