<?php

namespace Swoft\Http\Server\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Collector\ControllerCollector;

/**
 * RequestMapping注解解析器
 *
 * @uses      RequestMappingParser
 * @version   2017年09月03日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class RequestMappingParser extends AbstractParser
{

    /**
     * RequestMapping注解解析
     *
     * @param string $className
     * @param RequestMapping $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null|mixed $propertyValue
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        $collector = ControllerCollector::getCollector();

        if (!isset($collector[$className])) {
            return;
        }

        // Collect requestMapping
        ControllerCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
    }
}
