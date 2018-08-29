<?php

namespace Swoft\Bean\Wrapper;

use Swoft\Bean\Annotation\BeforeStart;
use Swoft\Bean\Annotation\Inject;

/**
 * the wrapper of before start
 *
 * @uses      BeforeStartWrapper
 * @version   2018年01月13日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BeforeStartWrapper extends AbstractWrapper
{
    /**
     * 类注解
     *
     * @var array
     */
    protected $classAnnotations = [
        BeforeStart::class,
    ];

    /**
     * 属性注解
     *
     * @var array
     */
    protected $propertyAnnotations = [
        Inject::class,
    ];

    /**
     * 是否解析类注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations): bool
    {
        return isset($annotations[BeforeStart::class]);
    }

    /**
     * 是否解析属性注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParsePropertyAnnotations(array $annotations): bool
    {
        return isset($annotations[Inject::class]);
    }

    /**
     * 是否解析方法注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParseMethodAnnotations(array $annotations): bool
    {
        return false;
    }
}