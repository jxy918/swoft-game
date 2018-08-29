<?php

namespace Swoft\Sg\Bean\Wrapper;

use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Value;
use Swoft\Bean\Wrapper\AbstractWrapper;
use Swoft\Sg\Bean\Annotation\Fallback;

/**
 * Fallback wrapper
 */
class FallbackWrapper extends AbstractWrapper
{
    /**
     * @var array
     */
    protected $classAnnotations
        = [
            Fallback::class,
        ];

    /**
     * @var array
     */
    protected $propertyAnnotations
        = [
            Inject::class,
            Value::class,
        ];

    /**
     * @var array
     */
    protected $methodAnnotations = [];

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations): bool
    {
        return isset($annotations[Fallback::class]);
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParsePropertyAnnotations(array $annotations): bool
    {
        return isset($annotations[Inject::class]) || isset($annotations[Value::class]);
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParseMethodAnnotations(array $annotations): bool
    {
        return false;
    }
}