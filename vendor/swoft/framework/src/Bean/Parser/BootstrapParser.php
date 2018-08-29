<?php

namespace Swoft\Bean\Parser;

use Swoft\Bean\Annotation\Bootstrap;
use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Collector\BootstrapCollector;

/**
 * the parser of bootstrap annotation
 *
 * @uses      BootstrapParser
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class BootstrapParser extends AbstractParser
{
    /**
     * @param string    $className
     * @param Bootstrap $objectAnnotation
     * @param string    $propertyName
     * @param string    $methodName
     * @param mixed     $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $beanName = $className;
        $scope    = Scope::SINGLETON;

        BootstrapCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ""];
    }
}