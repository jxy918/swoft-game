<?php

namespace Swoft\Bean\Wrapper;

use Swoft\Bean\Annotation\After;
use Swoft\Bean\Annotation\AfterReturning;
use Swoft\Bean\Annotation\AfterThrowing;
use Swoft\Bean\Annotation\Around;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\Before;
use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\PointAnnotation;
use Swoft\Bean\Annotation\PointBean;
use Swoft\Bean\Annotation\PointExecution;

/**
 * the aspect of wrapper
 *
 * @uses      AspectWrapper
 * @version   2017年12月24日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class AspectWrapper extends AbstractWrapper
{
    /**
     * 类注解
     *
     * @var array
     */
    protected $classAnnotations = [
        Aspect::class,
        PointBean::class,
        PointAnnotation::class,
        PointExecution::class,
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
     * 方法注解
     *
     * @var array
     */
    protected $methodAnnotations = [
        Before::class,
        After::class,
        AfterReturning::class,
        AfterThrowing::class,
        Around::class,
    ];

    /**
     * 是否解析类注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations): bool
    {
        return isset($annotations[Aspect::class]);
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
        $after = isset($annotations[After::class]) || isset($annotations[AfterThrowing::class]) || isset($annotations[AfterReturning::class]);

        return isset($annotations[Before::class]) || isset($annotations[Around::class]) || $after;
    }
}
