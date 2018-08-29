<?php

namespace Swoft\Bean\Parser;

use PhpDocReader\PhpDocReader;

/**
 * Inject注解解析器
 *
 * @uses      InjectParser
 * @version   2017年09月03日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class InjectParser extends AbstractParser
{

    /**
     * Inject注解解析
     *
     * @param string $className
     * @param object $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null $propertyValue
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $injectValue = $objectAnnotation->getName();
        if (!empty($injectValue)) {
            return [$injectValue, true];
        }

        // phpdoc解析
        $phpReader = new PhpDocReader();
        $property = new \ReflectionProperty($className, $propertyName);
        $propertyClass = $phpReader->getPropertyClass($property);

        $isRef = true;
        $injectProperty = $propertyClass;
        return [$injectProperty, $isRef];
    }
}
