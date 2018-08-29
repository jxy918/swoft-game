<?php

namespace Swoft\View\Bean\Wrapper\Extend;

use Swoft\Bean\Wrapper\Extend\WrapperExtendInterface;
use Swoft\View\Bean\Annotation\View;

/**
 * Controller extend
 */
class ControllerExtend implements WrapperExtendInterface
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
        return [];
    }

    /**
     * @return array
     */
    public function getMethodAnnotations(): array
    {
        return [View::class,];
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
        return false;
    }

    /**
     * @param array $annotations
     *
     * @return bool
     */
    public function isParseMethodAnnotations(array $annotations): bool
    {
        return isset($annotations[View::class]);
    }
}