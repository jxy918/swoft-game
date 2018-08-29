<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Collector\ValidatorCollector;

/**
 * number parser
 *
 * @uses      NumberParser
 * @version   2017年12月04日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class NumberParser extends AbstractParser
{
    /**
     * @param string                        $className
     * @param \Swoft\Bean\Annotation\Number $objectAnnotation
     * @param string                        $propertyName
     * @param string                        $methodName
     * @param string|null                   $propertyValue
     *
     * @return null
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = "",
        string $methodName = "",
        $propertyValue = null
    ) {
        ValidatorCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
        return null;
    }
}
