<?php declare(strict_types=1);

namespace Swoft\Processor;

/**
 * Processor interface
 * @since 2.0
 */
interface ProcessorInterface
{
    /**
     * Handle processor
     *
     * Return `true` is to continue
     */
    public function handle(): bool;
}
