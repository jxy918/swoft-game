<?php

namespace Swoft\Process\Bean\Parser;

use Swoft\Bean\Annotation\Scope;
use Swoft\Bean\Parser\AbstractParser;
use Swoft\Process\Bean\Annotation\Process;
use Swoft\Process\Bean\Collector\ProcessCollector;

/**
 * the parser of bootstrap process
 *
 * @uses      BootProcessParser
 * @version   2018年01月12日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ProcessParser extends AbstractParser
{
    /**
     * @param string  $className
     * @param Process $objectAnnotation
     * @param string  $propertyName
     * @param string  $methodName
     * @param mixed   $propertyValue
     *
     * @return array
     */
    public function parser(string $className, $objectAnnotation = null, string $propertyName = "", string $methodName = "", $propertyValue = null)
    {
        $scope    = Scope::PROTOTYPE;
        $name     = $objectAnnotation->getName();
        $beanName = empty($name) ? $className : $name;

        ProcessCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);

        return [$beanName, $scope, ""];
    }
}