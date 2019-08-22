<?php declare(strict_types=1);

namespace Swoft\Contract;

/**
 * Application interface
 * @since 2.0
 */
interface ApplicationInterface
{
    /**
     * Run application
     */
    public function run(): void;
}
