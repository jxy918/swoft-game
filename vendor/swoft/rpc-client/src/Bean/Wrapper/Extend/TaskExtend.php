<?php

namespace Swoft\Rpc\Client\Bean\Wrapper\Extend;

use Swoft\Bean\Wrapper\Extend\WrapperExtendInterface;
use Swoft\Rpc\Client\Bean\Annotation\Reference;

/**
 * Task extend
 */
class TaskExtend implements WrapperExtendInterface
{
    /**
     * @return array
     */
    public function getClassAnnotations(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getPropertyAnnotations(): array
    {
        return [Reference::class,];
    }

    /**
     * @return array
     */
    public function getMethodAnnotations(): array
    {
        return [];
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations): bool
    {
        return false;
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParsePropertyAnnotations(array $annotations): bool
    {
        return isset($annotations[Reference::class]);
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