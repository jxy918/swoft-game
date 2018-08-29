<?php

namespace Swoft\Bean\Parser;

/**
 * Annotation Parser Interface
 */
interface ParserInterface
{
    /**
     * 解析注解
     *
     * @param string      $className
     * @param object      $objectAnnotation
     * @param string      $propertyName
     * @param string      $methodName
     * @param string|null $propertyValue
     *
     * @return mixed
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    );
}
