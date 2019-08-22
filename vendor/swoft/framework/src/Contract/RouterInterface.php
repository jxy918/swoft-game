<?php

namespace Swoft\Contract;

/**
 * Interface RouterInterface - base interface for service router
 * @since 2.0
 */
interface RouterInterface
{
    /**
     * Found route
     */
    public const FOUND     = 1;

    /**
     * Not found
     */
    public const NOT_FOUND = 2;
}
