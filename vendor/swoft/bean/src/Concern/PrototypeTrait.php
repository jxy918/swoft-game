<?php declare(strict_types=1);

namespace Swoft\Bean\Concern;

use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;

/**
 * Class Prototype
 *
 * @since 2.0
 */
trait PrototypeTrait
{
    /**
     * Get instance from container
     *
     * @return static
     * @throws ReflectionException
     * @throws ContainerException
     */
    protected static function __instance()
    {
        return bean(static::class);
    }
}
