<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\ExceptionHandler;
use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Collector\ExceptionHandlerCollector;

/**
 * the parser of exception handler
 *
 * @uses      ExceptionHandlerParser
 * @version   2018年01月17日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ExceptionHandlerParser extends AbstractParser
{
    /**
     * Listen注解解析
     *
     * @param string           $className
     * @param ExceptionHandler $objectAnnotation
     * @param string           $propertyName
     * @param string           $methodName
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $beanName = $className;
        $scope    = Scope::SINGLETON;
        ExceptionHandlerCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ""];
    }
}