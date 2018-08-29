<?php

namespace Swoft\Console\Bean\Parser;

use Swoft\Bean\Parser\AbstractParser;
use Swoft\Console\Bean\Annotation\Mapping;
use Swoft\Console\Bean\Collector\CommandCollector;

/**
 * 抽象解析器
 *
 * @uses      AbstractParser
 * @version   2017年09月03日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class MappingParser extends AbstractParser
{
    /**
     * @param string $className
     * @param Mapping $objectAnnotation
     * @param string $propertyName
     * @param string $methodName
     * @param null $propertyValue
     * @return void
     */
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    ) {
        CommandCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
    }
}
