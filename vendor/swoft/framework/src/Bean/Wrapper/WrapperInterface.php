<?php

namespace Swoft\Bean\Wrapper;

use Swoft\Bean\Wrapper\Extend\WrapperExtendInterface;

/**
 * Annotation Wrapper Interface
 */
interface WrapperInterface
{
    /**
     * 封装注解
     *
     * @param string $className
     * @param array  $annotations
     * @return array|null
     */
    public function doWrapper(string $className, array $annotations);

    /**
     * 是否解析类注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations): bool;

    /**
     * 是否解析属性注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParsePropertyAnnotations(array $annotations): bool;

    /**
     * 是否解析方法注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParseMethodAnnotations(array $annotations): bool;

    /**
     * @param WrapperExtendInterface $extend
     */
    public function addExtends(WrapperExtendInterface $extend);
}
