<?php

namespace Swoft\Bean\Wrapper;

use Swoft\Bean\Annotation\ExceptionHandler;
use Swoft\Bean\Annotation\Handler;
use Swoft\Bean\Annotation\Inject;
use Swoft\Bean\Annotation\Value;

/**
 * the wrapper of exception handler
 *
 * @uses      ExceptionHandlerWrapper
 * @version   2018年01月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ExceptionHandlerWrapper extends AbstractWrapper
{
    /**
     * 类注解
     *
     * @var array
     */
    protected $classAnnotations = [
        ExceptionHandler::class,
    ];

    /**
     * 属性注解
     *
     * @var array
     */
    protected $propertyAnnotations = [
        Inject::class,
        Value::class,
    ];

    /**
     * the annotations of method
     *
     * @var array
     */
    protected $methodAnnotations = [
        Handler::class,
    ];

    /**
     * 是否解析类注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations): bool
    {
        return isset($annotations[ExceptionHandler::class]);
    }

    /**
     * 是否解析属性注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParsePropertyAnnotations(array $annotations): bool
    {
        return isset($annotations[Inject::class]) || isset($annotations[Value::class]);
    }

    /**
     * 是否解析方法注解
     *
     * @param array $annotations
     * @return bool
     */
    public function isParseMethodAnnotations(array $annotations): bool
    {
        return isset($annotations[Handler::class]);
    }
}