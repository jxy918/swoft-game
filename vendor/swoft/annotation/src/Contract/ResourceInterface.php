<?php declare(strict_types=1);


namespace Swoft\Annotation\Contract;

/**
 * Class ResourceInterface
 *
 * @since 2.0
 */
interface ResourceInterface
{
    /**
     * Load annotation resource
     */
    public function load(): void;
}